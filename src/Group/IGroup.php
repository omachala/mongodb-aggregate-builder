<?php

namespace AggregateBuilder\Group;

/**
 * Interface IGroup
 * @package AggregateBuilder\Group
 */
interface IGroup {

    const KEY = '_id';

    public function getProjectedColumnName();

    public function getQueryArray(): array;

}