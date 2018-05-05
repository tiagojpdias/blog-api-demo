<?php

namespace App\Filters;

class PostEloquentFilter extends AbstractEloquentFilter implements PostFilter
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'posts';

    /**
     * Author IDs.
     *
     * @var int
     */
    protected $authors;

    /**
     * Post published?
     *
     * @var bool
     */
    protected $published;

    /**
     * {@inheritdoc}
     */
    public function withAuthors(array $authors): PostFilter
    {
        $this->authors = $authors;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withPublished(bool $published): PostFilter
    {
        $this->published = $published;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function validSortColumns(): array
    {
        return [
            'id',
            'title',
            'slug',
            'content',
            'published_at',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function applyTo($queryBuilder): void
    {
        parent::applyTo($queryBuilder);

        // Apply search pattern filter
        if ($this->patterns) {
            $queryBuilder->where(function ($query) {
                foreach ($this->patterns as $pattern) {
                    $query->orWhere('posts.title', 'LIKE', '%'.$pattern.'%')
                        ->orWhere('posts.slug', 'LIKE', '%'.$pattern.'%')
                        ->orWhere('posts.content', 'LIKE', '%'.$pattern.'%');
                }
            });
        }

        // Apply Author ID filter
        if ($this->authors) {
            $queryBuilder->whereIn('posts.author_id', $this->authors);
        }

        // Apply published state filter
        if ($this->published !== null) {
            $queryBuilder->{$this->published ? 'whereNotNull' : 'whereNull'}('posts.published_at');
        }
    }
}
