<?php

namespace AggregateBuilder\Sort;

/**
 * Class SortDesc
 * @package AggregateBuilder\Sort
 */
class SortDesc extends ASort {

    /**
     * SortDesc constructor.
     * @param string $fieldName
     */
    public function __construct(string $fieldName) {
        parent::__construct($fieldName, -1);
    }
}