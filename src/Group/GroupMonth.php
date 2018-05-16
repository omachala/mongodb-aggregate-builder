<?php

namespace AggregateBuilder\Group;

/**
 * Class GroupMonth
 * @package AggregateBuilder\Group
 */
class GroupMonth extends GroupTime {

    /** @var string */
    private $projectedColumnName;

    /**
     * GroupMonth constructor.
     * @param string $projectedColumnName
     */
    public function __construct(string $projectedColumnName) {
        $this->projectedColumnName = $projectedColumnName;
        $this->setYear()->setMonth();
    }

    /**
     * @return string
     */
    public function getProjectedColumnName(): string {
        return $this->projectedColumnName;
    }


}