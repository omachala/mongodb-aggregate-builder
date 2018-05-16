<?php

namespace AggregateBuilder\Group;

/**
 * Class GroupTime
 * @package AggregateBuilder\Group
 */
abstract class GroupTime implements IGroup {

    /** @var array */
    private $group = [];

    /** @var bool */
    private $year = false;

    /** @var bool */
    private $month = false;

    /** @var bool */
    private $dayOfWeek = false;

    /** @var bool */
    private $dayOfMonth = false;

    /** @var bool */
    private $hour = false;

    /** @var bool */
    private $minute = false;

    /** @var bool */
    private $second = false;

    abstract function getProjectedColumnName();

    /**
     * @return array
     */
    public function getQueryArray(): array {
        $this->group = null;
        foreach (['year', 'month', 'dayOfWeek', 'dayOfMonth', 'hour', 'minute', 'second'] as $frequency) {
            $this->addToGroupIfEnabled($frequency);
        }
        return [IGroup::KEY => $this->group];
    }

    /**
     * @param string $frequency
     */
    private function addToGroupIfEnabled(string $frequency) {
        if ($this->{$frequency}) {
            $this->group[$frequency] = [
                '$' . $frequency => '$' . $this->getProjectedColumnName()
            ];
        }
    }

    /**
     * @param bool $year
     * @return GroupTime
     */
    protected function setYear(bool $year = true): GroupTime {
        $this->year = $year;
        return $this;
    }

    /**
     * @param bool $month
     * @return GroupTime
     */
    protected function setMonth(bool $month = true): GroupTime {
        $this->month = $month;
        return $this;
    }

    /**
     * @param bool $dayOfWeek
     * @return GroupTime
     */
    protected function setDayOfWeek(bool $dayOfWeek = true): GroupTime {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    /**
     * @param bool $dayOfMonth
     * @return GroupTime
     */
    protected function setDayOfMonth(bool $dayOfMonth = true): GroupTime {
        $this->dayOfMonth = $dayOfMonth;
        return $this;
    }

    /**
     * @param bool $hour
     * @return GroupTime
     */
    protected function setHour(bool $hour = true): GroupTime {
        $this->hour = $hour;
        return $this;
    }

    /**
     * @param bool $minute
     * @return GroupTime
     */
    protected function setMinute(bool $minute = true): GroupTime {
        $this->minute = $minute;
        return $this;
    }

    /**
     * @param bool $second
     * @return GroupTime
     */
    protected function setSecond(bool $second = true): GroupTime {
        $this->second = $second;
        return $this;
    }


}