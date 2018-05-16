<?php

namespace AggregateBuilder\Group;

/**
 * Class GroupDay
 * @package AggregateBuilder\Group
 */
class GroupDay extends GroupTime {

    /** @var string */
    private $projectedColumnName;

    /**
     * GroupDay constructor.
     * @param string $projectedColumnName
     */
    public function __construct(string $projectedColumnName) {
        $this->projectedColumnName = $projectedColumnName;
        $this->setYear()->setMonth()->setDayOfMonth();
    }

    /**
     * @return string
     */
    public function getProjectedColumnName(): string {
        return $this->projectedColumnName;
    }


}