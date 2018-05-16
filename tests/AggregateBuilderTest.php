<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * Class EnvironmentTest
 */
class AggregationBuilderTest extends TestCase {

    public function testEmptyInstance() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $this->assertSame([], $builder->getQueryArray());
    }

    public function testLimit() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setLimit(10);
        $this->assertSame([['$limit' => 10]], $builder->getQueryArray());
    }

    public function testSortAsc() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setSort(new \AggregateBuilder\Sort\SortAsc('_id'));
        $this->assertSame('[{"$sort":{"_id":1}}]', json_encode($builder));
    }

    public function testSortDesc() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setSort(new \AggregateBuilder\Sort\SortDesc('_id'));
        $this->assertSame('[{"$sort":{"_id":-1}}]', json_encode($builder));
    }

    public function testRawPathWithoutNest() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setRawPath('order.line_items');
        // Empty array because no nesting needed
        $this->assertSame([], $builder->getQueryArray());
    }

    public function testRawPathWithNest() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setNestedArrayDelimiter('xx');
        $builder->setRawPath('order.line_items.xx.quantity');
        $expect = '[{"$project":{"line_items":"$order.line_items"}},{"$unwind":"$line_items"}]';
        $this->assertSame($expect, json_encode($builder));
    }


    public function testGroup() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setGroup(new \AggregateBuilder\Group\GroupDay('_date'), \AggregateBuilder\AggregateBuilder::OPERATOR_COUNT);
        $expect = '[{"$group":{"_id":{"year":{"$year":"$_date"},"month":{"$month":"$_date"},"dayOfMonth":{"$dayOfMonth":"$_date"}},"result":{"$sum":1}}},{"$sort":{"_id":1}}]';
        $this->assertSame($expect, json_encode($builder));
    }

    public function testNestGrouped() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setGroup(new \AggregateBuilder\Group\GroupAll(), \AggregateBuilder\AggregateBuilder::OPERATOR_AVG);
        $builder->setRawPath('order.line_items.__.quantity');
        $expect = '[{"$project":{"line_items":"$order.line_items"}},{"$unwind":"$line_items"},{"$group":{"_id":null,"result":{"$avg":"$line_items.quantity"}}}]';
        $this->assertSame($expect, json_encode($builder));
    }

    public function testDateRange() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $dateRange = new \AggregateBuilder\Utils\DateRange(new DateTime('2017-01-01 00:00:00'), new DateTime('2018-12-31 00:00:00'));
        $builder->setDateRange($dateRange, '_date');
        $expect = '[{"$match":{"_date":{"$gte":{"$date":{"$numberLong":"1483228800000"}},"$lte":{"$date":{"$numberLong":"1546214400000"}}}}}]';
        $this->assertSame($expect, json_encode($builder));
    }

    public function testDateRangeWithNestGrouped() {
        $builder = new \AggregateBuilder\AggregateBuilder();
        $builder->setGroup(new \AggregateBuilder\Group\GroupAll(), \AggregateBuilder\AggregateBuilder::OPERATOR_AVG);
        $builder->setRawPath('order.line_items.__.variant.__.price');
        $dateRange = new \AggregateBuilder\Utils\DateRange(new DateTime('2017-01-01'), new DateTime('2018-12-31'));
        $builder->setDateRange($dateRange, '_date');
        $expect = '[{"$match":{"_date":{"$gte":{"$date":{"$numberLong":"1483228800000"}},"$lte":{"$date":{"$numberLong":"1546214400000"}}}}},{"$project":{"line_items":"$order.line_items"}},{"$unwind":"$line_items"},{"$unwind":"$line_items.variant"},{"$group":{"_id":null,"result":{"$avg":"$line_items.variant.price"}}},{"$sort":{"_date":-1}}]';
        $this->assertSame($expect, json_encode($builder));
    }


}
