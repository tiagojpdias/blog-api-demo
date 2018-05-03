<?php

namespace App\Filters;

abstract class AbstractEloquentFilter implements AbstractFilter
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table;

    /**
     * Columns to be selected.
     *
     * @var array
     */
    protected $columns = ['*'];

    /**
     * Sort column.
     *
     * @var string
     */
    protected $sortColumn = 'created_at';

    /**
     * Sort order.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Search patterns.
     *
     * @var array
     */
    protected $patterns = [];

    /**
     * Page number.
     *
     * @var int
     */
    protected $pageNumber = 1;

    /**
     * Items per page.
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * {@inheritdoc}
     */
    public function setPageNumber(int $number): AbstractFilter
    {
        $this->pageNumber = $number;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setItemsPerPage(int $items): AbstractFilter
    {
        $this->perPage = $items;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * {@inheritdoc}
     */
    public function column(string $name): string
    {
        return sprintf('%s.%s', $this->table, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function sortBy(string $column, string $order): AbstractFilter
    {
        $this->sortColumn = $column;
        $this->sortOrder = $order;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withSearchPattern(string $pattern): AbstractFilter
    {
        foreach (preg_split('/[\s,;]+/', $pattern, null, PREG_SPLIT_NO_EMPTY) as $search) {
            // Normalise search patterns
            $this->patterns[] = strtolower(trim($search, '%'));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumns(array $columns): AbstractFilter
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTo($queryBuilder): void
    {
        // Specify which columns to retrieve
        $queryBuilder->addSelect($this->getColumns());
    }
}
