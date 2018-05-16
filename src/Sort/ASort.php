<?php

namespace AggregateBuilder\Sort;

/**
 * Class ASort
 * @package AggregateBuilder\Sort
 */
abstract class ASort implements ISort {

    /** @var string */
    private $fieldName = '';

    /** @var int */
    private $direction = 1;

    /**
     * ASort constructor.
     * @param string $fieldName
     * @param int $direction
     */
    public function __construct(string $fieldName, int $direction = 1) {
        $this->fieldName = $fieldName;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getFieldName(): string {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     * @return ASort
     */
    public function setFieldName(string $fieldName): ASort {
        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * @return int
     */
    public function getDirection(): int {
        return $this->direction;
    }

    /**
     * @param int $direction
     * @return ASort
     */
    protected function setDirection(int $direction): ASort {
        $this->direction = $direction;
        return $this;
    }


}