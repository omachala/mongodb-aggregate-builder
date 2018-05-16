<?php

namespace AggregateBuilder\Group;

/**
 * Class GroupAll
 * @package AggregateBuilder\Group
 */
class GroupAll implements IGroup {

    /**
     * @return array
     */
    public function getQueryArray(): array {
        return [IGroup::KEY => null];
    }

    /**
     * @return null
     */
    public function getProjection() {
        return null;
    }

    /**
     * @return null
     */
    public function getProjectedColumnName() {
        return null;
    }
}