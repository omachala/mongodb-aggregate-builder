<?php

namespace AggregateBuilder\Group;

/**
 * Class GroupColumn
 * @package AggregateBuilder\Group
 */
class GroupColumn implements IGroup {

    /** @var string */
    private $columnName;

    /**
     * GroupColumn constructor.
     * @param string $columnName
     */
    public function __construct(string $columnName) {
        $this->columnName = $columnName;
    }

    /**
     * @return array
     */
    public function getQueryArray(): array {
        return [IGroup::KEY => $this->columnName];
    }

    /**
     * @return string
     */
    public function getProjectedColumnName() {
        return $this->columnName;
    }
}