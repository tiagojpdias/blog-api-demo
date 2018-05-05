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

        // Apply sorting
        if ($this->sortColumn) {
            switch ($this->sortColumn) {
                case 'title':
                case 'slug':
                case 'content':
                case 'published_at':
                case 'updated_at':
                case 'created_at':
                    $queryBuilder->orderBy($this->column($this->sortColumn), $this->sortOrder);
                    break;

                default:
                    $queryBuilder->orderBy('posts.created_at', $this->sortOrder);
                    break;
            }
        }
    }
}
