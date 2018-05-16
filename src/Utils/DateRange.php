<?php

namespace AggregateBuilder\Utils;

use MongoDB\BSON\UTCDateTime;

/**
 * Class DateRange
 * @package AggregateBuilder\Utils
 */
class DateRange {

    /** @var \DateTime */
    private $from;

    /** @var \DateTime|null */
    private $to;

    /** @var \DateTimeZone */
    private $zone;

    /**
     * DateRange constructor.
     * @param string|int|\DateTime $from
     * @param string|\DateTime $to
     * @param string $zone
     */
    public function __construct($from = '1970-01-01', $to = 'now', string $zone = 'UTC') {
        $this->zone = new \DateTimeZone($zone);

        if ($from instanceof \DateTime) {
            $this->from = $from;
        } else {
            $this->from = new \DateTime($from, $this->zone);
        }

        if ($to) {
            if ($to instanceof \DateTime) {
                $this->to = $to;
            } else {
                $this->to = new \DateTime($to, $this->zone);
            }
        }
    }

    /**
     * @return \DateTimeZone
     */
    private function getUtcTimeZone() {
        return new \DateTimeZone('UTC');
    }

    /**
     * @return UTCDateTime
     */
    public function getMongoDateFrom(): UTCDateTime {
        $ms = $this->getFrom()->getTimestamp() * 1000;
        return new UTCDateTime($ms);
    }

    /**
     * @return UTCDateTime
     */
    public function getMongoDateTo(): UTCDateTime {
        $ms = $this->getTo()->getTimestamp() * 1000;
        return new UTCDateTime($ms);
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): \DateTime {
        return $this->from;
    }

    /**
     * @return \DateTime|null
     */
    public function getTo() {
        return $this->to;
    }


    /**
     * @return \DateTimeZone
     */
    public function getZone(): \DateTimeZone {
        return $this->zone;
    }


}