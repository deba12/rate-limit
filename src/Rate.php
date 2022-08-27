<?php

declare(strict_types=1);

namespace RateLimit;

use InvalidArgumentException;

class Rate
{
    protected int $operations;
    protected int $interval;

    final protected function __construct(int $operations, int $interval)
    {
        if($operations < 1) {
            throw new InvalidArgumentException('Operations must be greater than 0');
        }

        if($interval < 1) {
            throw new InvalidArgumentException('Interval must be greater than 0');
        }

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
