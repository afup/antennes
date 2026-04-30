<?php

declare(strict_types=1);

namespace App\Tenant;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem(index: 'tenant', priority: 150)]
final readonly class TenantValueResolver implements ValueResolverInterface
{
    public function __construct(
        private TenantFetcher $tenantFetcher,
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

        return [$this->tenantFetcher->fromRequest($request)];
    }
}
