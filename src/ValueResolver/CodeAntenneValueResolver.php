<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Dto\Antenne;
use App\Repository\AntennesRepository;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem(index: 'antenne_code', priority: 150)]
final readonly class CodeAntenneValueResolver implements ValueResolverInterface
{
    public function __construct(
        private AntennesRepository $antennesRepository,
    ) {}

    /**
     * @return iterable<Antenne>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== Antenne::class) {
            return [];
        }

        if (!$request->attributes->has('code')) {
            throw new CodeAntenneInvalideException();
        }

        $code = $request->attributes->get('code');
        if ($code === null) {
            throw new CodeAntenneInvalideException();
        }

        if ($code === 'aix-marseille') {
            $code = 'marseille';
        } elseif ($code === 'hdf') {
            $code = 'lille';
        }

        if (!is_string($code)) {
            throw new CodeAntenneInvalideException();
        }

        $antenne = $this->antennesRepository->get($code);
        if ($antenne === null) {
            throw new CodeAntenneInvalideException();
        }

        return [
            $antenne,
        ];
    }
}
