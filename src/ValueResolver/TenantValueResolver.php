<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Repository\AntennesRepository;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem(index: 'tenant', priority: 150)]
final readonly class TenantValueResolver implements ValueResolverInterface
{
    public function __construct(
        private AntennesRepository $antennesRepository,
    ) {}

    /**
     * @return iterable<Tenant>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== Tenant::class) {
            return [];
        }

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

        return [
            new Tenant($subdomain, $antenne),
        ];
    }
}
