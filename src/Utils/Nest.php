<?php

namespace AggregateBuilder\Utils;

use AggregateBuilder\Group\IGroup;
use AggregateBuilder\Sort\ISort;

/**
 * Class Nest
 * @package AggregateBuilder
 */
class Nest {

    const OPERATOR_SORT = '$sort';
    const OPERATOR_PROJECT = '$project';
    const OPERATOR_UNWIND = '$unwind';
    const OPERATOR_MATCH = '$match';
    const OPERATOR_GROUP = '$group';
    const OPERATOR_LIMIT = '$limit';


    /** @var array[] */
    private $queries = [];

    /**
     * @return array
     */
    public function toArray() {
        return array_values(array_filter($this->queries));
    }


    private function addQuery(string $operator, $content) {
        $this->queries[] = [$operator => $content];
    }

    /**
     * @param string $operator
     * @return array
     */
    private function getQuery(string $operator) {
        if (array_key_exists($operator, $this->queries)) {
            return $this->queries[$operator];
        }
        return [];
    }

    /**
     * @param string $projectedPath
     * @param $value
     */
    public function addMatch(string $projectedPath, $value) {
        $this->addQuery(static::OPERATOR_MATCH, [$projectedPath => $value]);
    }

    /**
     * @param $fullPath
     */
    public function addProject($fullPath) {
        $pathSplits = explode('.', $fullPath);
        $content = [end($pathSplits) => '$' . $fullPath];
        $this->addQuery(static::OPERATOR_PROJECT, $content);
    }

    /**
     * @param ISort $sort
     */
    public function addSort(ISort $sort) {
        $pathSplits = explode('.', $sort->getFieldName());
        $content = [end($pathSplits) => $sort->getDirection()];
        $this->addQuery(static::OPERATOR_SORT, $content);
    }

    /**
     * @param string $projectedPath
     */
    public function addUnwind(string $projectedPath) {
        $this->addQuery(static::OPERATOR_UNWIND, '$' . $projectedPath);
    }

    /**
     * @param IGroup $group
     * @param $projectedPath
     * @param string $operator
     * @param string $projectedPathPrefix
     */
    public function addGroup(IGroup $group, $projectedPath, string $operator, string $projectedPathPrefix = '$') {
        $projectedPathString = is_array($projectedPath) ? implode('.', $projectedPath) : $projectedPath;

        $projectedColumnName = $group->getProjectedColumnName();
        if ($projectedColumnName && is_array($projectedPath) && count($projectedPath) > 1) {
            $this->addProject($projectedColumnName);
        }

        $path = 1;
        if ($projectedPathPrefix && $projectedPathString) {
            $path = (string)$projectedPathPrefix . $projectedPathString;
        } elseif (is_int($projectedPathString)) {
            $path = (int)$projectedPathString;
        }

        $query = $group->getQueryArray();
        $query['result'] = [$operator => $path];
        $this->addQuery(static::OPERATOR_GROUP, $query);
    }

    /**
     * @param int $limit
     */
    public function addLimit(int $limit) {
        $this->addQuery(static::OPERATOR_LIMIT, $limit);
    }

}