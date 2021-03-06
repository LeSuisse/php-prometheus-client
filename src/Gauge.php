<?php

declare(strict_types=1);

namespace Enalean\Prometheus;

use Enalean\Prometheus\Storage\GaugeStorage;
use Enalean\Prometheus\Value\MetricLabelNames;
use Enalean\Prometheus\Value\MetricName;

/**
 * @extends Metric<MetricLabelNames>
 */
final class Gauge extends Metric
{
    /** @var GaugeStorage */
    private $storage;

    public function __construct(GaugeStorage $storage, MetricName $name, string $help, ?MetricLabelNames $labelNames = null)
    {
        parent::__construct($name, $help, $labelNames ?? MetricLabelNames::fromNames());
        $this->storage = $storage;
    }

    /**
     * @param float  $value          e.g. 123
     * @param string ...$labelValues e.g. ['status', 'opcode']
     */
    public function set(float $value, string ...$labelValues): void
    {
        $this->assertLabelsAreDefinedCorrectly(...$labelValues);

        $this->storage->setGaugeTo(
            $this->getName(),
            $value,
            $this->getHelp(),
            $this->getLabelNames(),
            ...$labelValues
        );
    }

    /**
     * @param string ...$labelValues e.g. ['status', 'opcode']
     */
    public function inc(string ...$labelValues): void
    {
        $this->incBy(1, ...$labelValues);
    }

    /**
     * @param string ...$labelValues e.g. ['status', 'opcode']
     */
    public function incBy(float $value, string ...$labelValues): void
    {
        $this->assertLabelsAreDefinedCorrectly(...$labelValues);

        $this->storage->addToGauge(
            $this->getName(),
            $value,
            $this->getHelp(),
            $this->getLabelNames(),
            ...$labelValues
        );
    }

    /**
     * @param string ...$labelValues e.g. ['status', 'opcode']
     */
    public function dec(string ...$labelValues): void
    {
        $this->decBy(1, ...$labelValues);
    }

    /**
     * @param string ...$labelValues e.g. ['status', 'opcode']
     */
    public function decBy(float $value, string ...$labelValues): void
    {
        $this->incBy(-$value, ...$labelValues);
    }
}
