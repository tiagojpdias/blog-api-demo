<?php

namespace App\Filters;

interface PostFilter extends AbstractFilter
{
    /**
     * Add Author IDs for filtering.
     *
     * @param array $authors
     *
     * @return PostFilter
     */
    public function withAuthors(array $authors): PostFilter;

    /**
     * Add published filtering.
     *
     * @param bool $published
     *
     * @return PostFilter
     */
    public function withPublished(bool $published): PostFilter;
}
