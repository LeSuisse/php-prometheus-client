<?php

declare(strict_types=1);

namespace Test\Prometheus\Storage\Redis;

use Prometheus\Registry\CollectorRegistry;
use Prometheus\Renderer\RenderTextFormat;
use Prometheus\Storage\RedisStore;
use Test\Prometheus\CollectorRegistryBaseTest;

/**
 * @requires extension redis
 */
final class CollectorRegistryTest extends CollectorRegistryBaseTest
{
    use ConfigureRedisStorage;

    /**
     * @test
     */
    public function itShouldOnlyFlushMetricData() : void
    {
        $redis = $this->getRedisClient();
        $redis->set('foo', 'bar');

        $registry = new CollectorRegistry($this->adapter);

        $counter = $registry->registerCounter('test', 'some_counter', 'it increases', ['type']);
        $counter->incBy(6, ['blue']);
        $counterRedisKey = 'PROMETHEUS_counter' . RedisStore::PROMETHEUS_METRIC_KEYS_SUFFIX;
        $this->assertEquals(['PROMETHEUS_:counter:test_some_counter'], $redis->sMembers($counterRedisKey));

        $gauge = $registry->registerGauge('test', 'some_gauge', 'this is for testing', ['foo']);
        $gauge->set(35, ['bar']);
        $gaugeRedisKey = 'PROMETHEUS_gauge' . RedisStore::PROMETHEUS_METRIC_KEYS_SUFFIX;
        $this->assertEquals(['PROMETHEUS_:gauge:test_some_gauge'], $redis->sMembers($gaugeRedisKey));

        $histogram = $registry->registerHistogram('test', 'some_histogram', 'this is for testing', ['foo', 'bar'], [0.1, 1, 5, 10]);
        $histogram->observe(2, ['cat', 'meow']);
        $histogramRedisKey = 'PROMETHEUS_histogram' . RedisStore::PROMETHEUS_METRIC_KEYS_SUFFIX;
        $this->assertEquals(['PROMETHEUS_:histogram:test_some_histogram'], $redis->sMembers($histogramRedisKey));

        $this->adapter->flush();

        $this->assertEquals('bar', $redis->get('foo'));

        $this->assertEquals([], $redis->sMembers($counterRedisKey));
        $this->assertFalse($redis->get('PROMETHEUS_:counter:test_some_counter'));
        $this->assertEquals([], $redis->sMembers($gaugeRedisKey));
        $this->assertFalse($redis->get('PROMETHEUS_:gauge:test_some_gauge'));
        $this->assertEquals([], $redis->sMembers($histogramRedisKey));
        $this->assertFalse($redis->get('PROMETHEUS_:histogram:test_some_histogram'));

        $this->assertEquals("\n", (new RenderTextFormat())->render($registry->getMetricFamilySamples()));

        $redis->del('foo');
    }
}
