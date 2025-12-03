<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class Meetup
{
    public function __construct(
        public string $title,
        public \DateTimeImmutable $date,
        public string $location,
        public string $description,
        public string $url,
    ) {}
}
