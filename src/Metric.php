<?php

declare(strict_types=1);

namespace Enalean\Prometheus;

use Enalean\Prometheus\Value\LabelNames;
use Enalean\Prometheus\Value\MetricName;
use InvalidArgumentException;
use function count;
use function print_r;
use function sprintf;

/**
 * @template TLabelNames of LabelNames
 * @psalm-immutable
 */
abstract class Metric
{
    /** @var MetricName */
    private $name;
    /** @var string */
    private $help;
    /**
     * @var LabelNames
     * @psalm-var TLabelNames
     * */
    private $labelNames;

    /**
     * @psalm-param TLabelNames $labelNames
     */
    public function __construct(MetricName $name, string $help, LabelNames $labelNames)
    {
        $this->name       = $name;
        $this->help       = $help;
        $this->labelNames = $labelNames;
    }

    public function getName() : MetricName
    {
        return $this->name;
    }

    /**
     * @psalm-return TLabelNames
     */
    public function getLabelNames() : LabelNames
    {
        return $this->labelNames;
    }

    public function getHelp() : string
    {
        return $this->help;
    }

    final protected function assertLabelsAreDefinedCorrectly(string ...$labelValues) : void
    {
        if (count($labelValues) !== count($this->labelNames)) {
            throw new InvalidArgumentException(sprintf('Labels are not defined correctly: %s', print_r($labelValues, true)));
        }
    }
}