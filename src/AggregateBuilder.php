<?php

namespace AggregateBuilder;


use AggregateBuilder\Group\GroupAll;
use AggregateBuilder\Group\GroupTime;
use AggregateBuilder\Group\IGroup;
use AggregateBuilder\Sort\ISort;
use AggregateBuilder\Sort\SortAsc;
use AggregateBuilder\Sort\SortDesc;
use AggregateBuilder\Utils\DateRange;
use AggregateBuilder\Utils\Nest;

/**
 * Class AggregateBuilder
 * @package AggregateBuilder
 */
class AggregateBuilder implements IAggregationBuilder {

    const OPERATOR_COUNT = 'count';
    const OPERATOR_SUM = '$sum';
    const OPERATOR_AVG = '$avg';
    const OPERATOR_MAX = '$max';
    const OPERATOR_MIN = '$min';
    const OPERATOR_LAST = '$last';
    const OPERATOR_FIRST = '$first';

    const OPERATOR_GREATER_THAN = '$gte';
    const OPERATOR_LOWER_THAN = '$lte';

    const PATH_GLUE = '.';


    /** @var string */
    private $nestedArrayDelimiter = '__';

    /** @var IGroup */
    private $group;

    /** @var ISort */
    private $sort;

    /** @var string */
    private $rawPath = '';

    /** @var string */
    private $groupAccumulatorOperator;

    /** @var DateRange */
    private $dateRange;

    /** @var string */
    private $dateAttr;

    /** @var array */
    private $nests = [];

    /** @var int|null */
    private $limit = null;

    /**
     * @param string $dateAttr
     * @return AggregateBuilder
     */
    public function setDateAttr(string $dateAttr): AggregateBuilder {
        $this->dateAttr = $dateAttr;
        return $this;
    }

    /**
     * @param DateRange $dateRange
     * @param string $dateAttr
     * @return AggregateBuilder
     */
    public function setDateRange(DateRange $dateRange, string $dateAttr = null): AggregateBuilder {
        if ($dateAttr) {
            $this->setDateAttr($dateAttr);
        }
        $this->dateRange = $dateRange;
        return $this;
    }

    /**
     * @param IGroup $group
     * @param $operator
     * @return AggregateBuilder
     */
    public function setGroup(IGroup $group, string $operator): AggregateBuilder {
        $this->groupAccumulatorOperator = $operator; // @todo move 'operator' to IGroup
        $this->group = $group;
        return $this;
    }

    /**
     * @return IGroup
     */
    public function getGroup() {

        if ($this->group === null && $this->getGroupAccumulatorOperator()) {
            $this->setGroup(new GroupAll(), $this->getGroupAccumulatorOperator());
        }

        return $this->group;
    }

    /**
     * @return ISort
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * @param ISort $sort
     * @return AggregateBuilder
     */
    public function setSort(ISort $sort): AggregateBuilder {
        $this->sort = $sort;
        return $this;
    }


    /**
     * @param string $rawPath
     * @return AggregateBuilder
     */
    public function setRawPath(string $rawPath): AggregateBuilder {
        $this->rawPath = $rawPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getNestedArrayDelimiter(): string {
        return $this->nestedArrayDelimiter;
    }

    /**
     * @param string $nestedArrayDelimiter
     * @return AggregateBuilder
     */
    public function setNestedArrayDelimiter(string $nestedArrayDelimiter): AggregateBuilder {
        $this->nestedArrayDelimiter = $nestedArrayDelimiter;
        return $this;
    }


    private function processRawPath() {

        $pathSplits = explode(static::PATH_GLUE, $this->getRawPath());

        if ($this->getDateRange()) {
            $nest = new Nest();
            $dateRangeMatch[static::OPERATOR_GREATER_THAN] = $this->getDateRange()->getMongoDateFrom();
            $dateRangeMatch[static::OPERATOR_LOWER_THAN] = $this->getDateRange()->getMongoDateTo();
            $nest->addMatch($this->getDateAttr(), $dateRangeMatch);
            $this->addNest($nest);
        }

        $unwind = false;
        $projected = false;
        $project = false;
        $projectedPath = [];
        $previousPath = "";
        $accumulatePath = [];

        for ($i = 0; $i < count($pathSplits); $i++) {

            $path = $pathSplits[$i];

            if ($path == $this->getNestedArrayDelimiter()) {
                $unwind = true;
                if (!$projected) {
                    $project = true;
                }
                continue;
            }

            if (!$project) {
                $accumulatePath[] = $path;
            }

            if ($project || $projected) {
                $projectedPath[] = $previousPath;
            }

            $nest = new Nest();

            if ($project) {
                $nest->addProject(implode(static::PATH_GLUE, $accumulatePath));
                $projected = true;
            }

            if ($unwind) {
                $nest->addUnwind(implode(static::PATH_GLUE, $projectedPath));
            }

            $this->addNest($nest);
            $unwind = false;
            $project = false;
            $previousPath = $path;
        }


        $lastNest = end($pathSplits);

        if (!$projected) {
            $lastNest = $this->getRawPath();
        }

        $projectedPath[] = $lastNest;

        $nest = new Nest();


        if (in_array($this->getGroupAccumulatorOperator(), [static::OPERATOR_COUNT])) {
            $nest->addGroup($this->getGroup(), 1, static::OPERATOR_SUM, '');
        }

        if (in_array($this->getGroupAccumulatorOperator(), [static::OPERATOR_AVG, static::OPERATOR_MAX, static::OPERATOR_MIN, static::OPERATOR_SUM, static::OPERATOR_FIRST, static::OPERATOR_LAST])) {
            $nest->addGroup($this->getGroup(), $projectedPath, $this->getGroupAccumulatorOperator());
        }

        if ($this->getGroup() && !$this->getGroupAccumulatorOperator()) {
            $nest->addGroup($this->getGroup(), $projectedPath, static::OPERATOR_SUM, '');
        }

        if ($this->getLimit()) {
            $nest->addLimit($this->getLimit());
        }

        if ($this->getSort()) {
            $nest->addSort($this->getSort());
        } else if ($this->getGroup() instanceof GroupAll && $this->getDateAttr()) {
            $nest->addSort(new SortDesc($this->getDateAttr()));
        } else if ($this->getGroup() instanceof GroupTime) {
            $nest->addSort(new SortAsc('_id'));
        }

        $this->addNest($nest);
    }


    /**
     * @return string
     */
    private function getRawPath(): string {
        return $this->rawPath;
    }

    /**
     * @return DateRange|null
     */
    public function getDateRange() {
        return $this->dateRange;
    }

    /**
     * @return string|null
     */
    public function getDateAttr() {
        return $this->dateAttr;
    }

    private function addNest(Nest $nest) {
        $this->nests[] = $nest;
    }

    /**
     * @return string|null
     */
    private function getGroupAccumulatorOperator() {
        return $this->groupAccumulatorOperator;
    }

    /**
     * @return array
     */
    public function getQueryArray() {
        $this->processRawPath();
        $output = [];

        /** @var Nest $nest */
        foreach ($this->getNests() as $nest) {
            $output = array_merge($output, $nest->toArray());
        }
        return $this->mergePartitions($output);
    }

    /**
     * Merge same keys ng. [{$project:{xx}}, {$project:{yy}}] --> [{$project:{xx, yy}}
     * @param array $partitions
     * @return array
     */
    private function mergePartitions(array $partitions) {
        $merged = [];
        $disableMarginFor = ['$unwind'];

        foreach ($partitions as $array => $value) {
            $key = key($value);
            if (in_array($key, $disableMarginFor)) {
                $merged[] = $value;
            } else {
                $merged[$key] = array_key_exists($key, $merged) ? array_merge_recursive($merged[$key], $value) : $value;
            }
        }

        return array_values($merged);
    }


    /**
     * @return array
     */
    private function getNests(): array {
        return $this->nests;
    }

    /**
     * @return int|null
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     * @return AggregateBuilder
     */
    public function setLimit(int $limit): AggregateBuilder {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
        return $this->getQueryArray();
    }
}