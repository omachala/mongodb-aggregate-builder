<?php

namespace AggregateBuilder\Sort;
/**
 * Class SortAsc
 * @package AggregateBuilder\Sort
 */
class SortAsc extends ASort {

    /**
     * SortAsc constructor.
     * @param string $fieldName
     */
    public function __construct(string $fieldName) {
        parent::__construct($fieldName);
    }
}