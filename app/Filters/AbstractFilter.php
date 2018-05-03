<?php

namespace App\Filters;

interface AbstractFilter
{
    /**
     * Set page number.
     *
     * @param int $number
     *
     * @return self
     */
    public function setPageNumber(int $number): self;

    /**
     * Get page number.
     *
     * @return int
     */
    public function getPageNumber(): int;

    /**
     * Set items per page.
     *
     * @param int $items
     *
     * @return self
     */
    public function setItemsPerPage(int $items): self;

    /**
     * Get items per page.
     *
     * @return int
     */
    public function getItemsPerPage(): int;

    /**
     * Get an alias prefixed column.
     *
     * @param string $name
     *
     * @return string
     */
    public function column(string $name): string;

    /**
     * Set the sorting column/order.
     *
     * @param string $column
     * @param string $order
     *
     * @return self
     */
    public function sortBy(string $column, string $order): self;

    /**
     * Add search pattern filtering.
     *
     * @param string $pattern
     *
     * @return self
     */
    public function withSearchPattern(string $pattern): self;

    /**
     * Get the columns to be selected.
     *
     * @return array
     */
    public function getColumns(): array;

    /**
     * Set the columns to be selected.
     *
     * @param array
     *
     * @return self
     */
    public function setColumns(array $columns): self;

    /**
     * Apply filters to a Query builder.
     *
     * @param mixed $queryBuilder
     *
     * @return void
     */
    public function applyTo($queryBuilder): void;
}
