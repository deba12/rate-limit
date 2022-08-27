<?php
/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace RateLimit;

use RateLimit\Exception\LimitExceeded;
use Redis;
use RedisException;
use function ceil;
use function max;
use function time;

final class RedisRateLimiter extends ConfigurableRateLimiter implements RateLimiter, SilentRateLimiter
{
    private Redis $redis;
    private string $keyPrefix;

    public function __construct(Rate $rate, Redis $redis, string $keyPrefix = '')
    {
        parent::__construct($rate);
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @param string $identifier
     * @return void
     * @throws RedisException
     */
    public function limit(string $identifier): void
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current >= $this->rate->getOperations()) {
            throw LimitExceeded::for($identifier, $this->rate);
        }

        $this->updateCounter($key);
    }

    /**
     * @param string $identifier
     * @return Status
     * @throws RedisException
     */
    public function limitSilently(string $identifier): Status
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current <= $this->rate->getOperations()) {
            $current = $this->updateCounter($key);
        }

        return Status::from(
            $identifier,
            $current,
            $this->rate->getOperations(),
            time() + $this->ttl($key)
        );
    }

    private function key(string $identifier): string
    {
        /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */
        return "{$this->keyPrefix}{$identifier}:{$this->rate->getInterval()}";
    }

    /**
     * @param string $key
     * @return int
     * @throws RedisException
     */
    private function getCurrent(string $key): int
    {
        return (int) $this->redis->get($key);
    }

    /**
     * @param string $key
     * @return int
     * @throws RedisException
     */
    private function updateCounter(string $key): int
    {
        $current = $this->redis->incr($key);

        if ($current === 1) {
            $this->redis->expire($key, $this->rate->getInterval());
        }

        return $current;
    }

    /**
     * @param string $key
     * @return int
     * @throws RedisException
     */
    private function ttl(string $key): int
    {
        return max((int) ceil($this->redis->pttl($key) / 1000), 0);
    }
}
