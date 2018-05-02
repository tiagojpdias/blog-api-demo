<?php

namespace App\Filters;

interface AbstractFilter
{
    /**
     * Set page number.
     *
     * @param int $number
     *
     * @return AbstractFilter
     */
    public function setPageNumber(int $number): AbstractFilter;

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
     * @return AbstractFilter
     */
    public function setItemsPerPage(int $items): AbstractFilter;

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
     * @return AbstractFilter
     */
    public function sortBy(string $column, string $order): AbstractFilter;

    /**
     * Add search pattern filtering.
     *
     * @param string $pattern
     *
     * @return AbstractFilter
     */
    public function withSearchPattern(string $pattern): AbstractFilter;

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
     * @return AbstractFilter
     */
    public function setColumns(array $columns): AbstractFilter;

    /**
     * Apply filters to a Query builder.
     *
     * @param mixed $queryBuilder
     *
     * @return void
     */
    public function applyTo($queryBuilder): void;
}
