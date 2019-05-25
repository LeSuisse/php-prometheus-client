<?php

declare(strict_types=1);

namespace Prometheus\Storage;

use Prometheus\Value\MetricName;

final class NullStore implements Store, CounterStorage, GaugeStorage, HistogramStorage
{
    /**
     * @inheritdoc
     */
    public function collect() : array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function updateHistogram(MetricName $name, array $data) : void
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function updateGauge(MetricName $name, array $data) : void
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function updateCounter(MetricName $name, array $data) : void
    {
        return;
    }
}
