<?php

declare(strict_types=1);

namespace RateLimit;

use Assert\Assertion;

class Rate
{
    protected int $operations;
    protected int $interval;

    final protected function __construct(int $operations, int $interval)
    {
        Assertion::greaterThan($operations, 0, 'Quota must be greater than zero');
        Assertion::greaterThan($interval, 0, 'Seconds interval must be greater than zero');

        $this->operations = $operations;
        $this->interval = $interval;
    }

    /**
     * @param int $operations
     * @return Rate
     */
    public static function perSecond(int $operations): Rate {
        return new static($operations, 1);
    }

    /**
     * @param int $operations
     * @return Rate
     */
    public static function perMinute(int $operations): Rate {
        return new static($operations, 60);
    }

    /**
     * @param int $operations
     * @return Rate
     */
    public static function perHour(int $operations): Rate {
        return new static($operations, 3600);
    }

    /**
     * @param int $operations
     * @return Rate
     */
    public static function perDay(int $operations): Rate {
        return new static($operations, 86400);
    }

    /**
     * @param int $operations
     * @param int $interval
     * @return Rate
     */
    public static function custom(int $operations, int $interval): Rate {
        return new static($operations, $interval);
    }

    /**
     * @return int
     */
    public function getOperations(): int {
        return $this->operations;
    }

    /**
     * @return int
     */
    public function getInterval(): int {
        return $this->interval;
    }

}
