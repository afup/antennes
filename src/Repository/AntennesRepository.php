<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Antenne;

interface AntennesRepository
{
    public function get(string $code): ?Antenne;
}
