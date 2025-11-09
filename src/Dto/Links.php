<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class Links
{
    public function __construct(
        public string $meetup,
        public ?string $linkedin,
        public ?string $bluesky,
    ) {
    }
}
