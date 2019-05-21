<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$adapter = $_GET['adapter'] ?? '';

if ($adapter === 'redis') {
    $redis_client = new Redis();
    $redis_client->connect($_SERVER['REDIS_HOST'] ?? '127.0.0.1');

    $redisAdapter = new Prometheus\Storage\RedisStore($redis_client);
    $redisAdapter->flushRedis();

    return;
}

if ($adapter === 'apcu') {
    $apcAdapter = new Prometheus\Storage\APCUStore();
    $apcAdapter->flushAPC();

    return;
}

if ($adapter === 'in-memory') {
    $inMemoryAdapter = new Prometheus\Storage\InMemoryStore();
    $inMemoryAdapter->flushMemory();

    return;
}
