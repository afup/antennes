<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class Antenne
{
    public function __construct(
        public string $code,
        public string $label,
        public Logo $logo,
        public Links $links,
        public ?Meetup $nextMeetup = null,
    ) {}
}
