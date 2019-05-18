<?php

declare(strict_types=1);

namespace Prometheus\Exception;

use Exception;

/**
 * Exception thrown if a metric can't be found in the CollectorRegistry.
 */
class MetricNotFoundException extends Exception
{
}
