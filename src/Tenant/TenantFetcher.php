<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Repository\AntennesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class TenantFetcher
{
    public function __construct(
        private AntennesRepository $antennesRepository,
        private RequestStack $requestStack,
    ) {}

    public function current(): Tenant
    {
        return $this->fromRequest(
            $this->requestStack->getCurrentRequest() ?? throw new \LogicException('No current request'),
        );
    }

    public function fromRequest(Request $request): Tenant
    {
        if (!$request->attributes->has('subdomain')) {
            throw new CodeAntenneInvalideException();
        }

        $code = $subdomain = $request->attributes->getString('subdomain', '');
        if ($subdomain === '') {
            throw new CodeAntenneInvalideException();
        }

        if ($subdomain === 'aix-marseille') {
            $code = 'marseille';
        } elseif ($subdomain === 'hdf') {
            $code = 'lille';
        }

        $antenne = $this->antennesRepository->get($code);
        if ($antenne === null) {
            throw new CodeAntenneInvalideException();
        }

        return new Tenant($subdomain, $antenne);
    }
}
