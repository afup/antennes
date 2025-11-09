<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class Logo
{
    public function __construct(
        public string $simple,
    ) {}
}
