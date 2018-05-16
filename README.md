# MongoDB Aggregate Builder
PHP tool that helps create MongoDB aggregation queries despite nested objects and array.

[![Travis](https://img.shields.io/travis/omachala/mongodb-aggregate-builder.svg)](https://travis-ci.org/omachala/mongodb-aggregate-builder)
[![Coveralls github](https://img.shields.io/coveralls/github/omachala/mongodb-aggregate-builder.svg)](https://coveralls.io/github/omachala/mongodb-aggregate-builder)

## Installation

Add this Github repository dependency into your composer.json

```json
{
  "require": {
    ...
    "omachala/mongodb-aggregate-builder": "master"
  },
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "omachala/mongodb-aggregate-builder",
        "version": "master",
        "source": {
          "url": "https://github.com/omachala/mongodb-aggregate-builder.git",
          "type": "git",
          "reference": "master"
        }
      }
    }
  ]
}
```

## Usage 

Example code:

```php
$builder = new \AggregateBuilder\AggregateBuilder();
$builder->setGroup(new \AggregateBuilder\Group\GroupAll(), \AggregateBuilder\AggregateBuilder::OPERATOR_AVG);
$builder->setRawPath('order.line_items.__.variant.__.price');
$dateRange = new \AggregateBuilder\Utils\DateRange(new DateTime('2017-01-01'), new DateTime('2018-12-31'));
$builder->setDateRange($dateRange, '_date');
echo json_encode($builder);
```

will produce this MongoDB aggregation query JSON:

```json
[
  {
    "$match": {
      "_date": {
        "$gte": {
          "$date": {
            "$numberLong": "1483228800000"
          }
        },
        "$lte": {
          "$date": {
            "$numberLong": "1546214400000"
          }
        }
      }
    }
  },
  {
    "$project": {
      "line_items": "$order.line_items"
    }
  },
  {
    "$unwind": "$line_items"
  },
  {
    "$unwind": "$line_items.variant"
  },
  {
    "$group": {
      "_id": null,
      "result": {
        "$avg": "$line_items.variant.price"
      }
    }
  },
  {
    "$sort": {
      "_date": -1
    }
  }
]
```

## License
MIT