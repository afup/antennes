<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Dto\Antenne;

final readonly class Tenant
{
    public function __construct(
        public string $subdomain,
        public Antenne $antenne,
    ) {}
}
