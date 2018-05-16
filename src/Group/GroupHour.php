<?php

namespace AggregateBuilder\Group;

/**
 * Class GroupHour
 * @package AggregateBuilder\Group
 */
class GroupHour extends GroupTime {

    /** @var string */
    private $projectedColumnName;

    /**
     * GroupHour constructor.
     * @param string $projectedColumnName
     */
    public function __construct(string $projectedColumnName) {
        $this->projectedColumnName = $projectedColumnName;
        $this->setYear()->setMonth()->setDayOfMonth()->setHour();
    }

    /**
     * @return string
     */
    public function getProjectedColumnName(): string {
        return $this->projectedColumnName;
    }


}