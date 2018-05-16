<?php

namespace AggregateBuilder\Sort;
/**
 * Interface ISort
 * @package AggregateBuilder\Sort
 */
interface ISort {

    public function getFieldName(): string;

    public function getDirection(): int;

}