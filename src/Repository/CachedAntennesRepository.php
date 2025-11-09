<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Antenne;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CachedAntennesRepository implements AntennesRepository
{
    public function __construct(
        private AntennesRepository $inner,
        private CacheInterface $cache,
    ) {}

    public function get(string $code): ?Antenne
    {
        return $this->cache->get('antenne_' . $code, function (ItemInterface $item) use ($code): ?Antenne {
            $item->expiresAfter(3600);

            return $this->inner->get($code);
        });
    }
}
