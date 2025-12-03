<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Antenne;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ApiAntennesRepository implements AntennesRepository
{
    public function __construct(
        #[Autowire(service: 'afup.client')]
        private HttpClientInterface $httpClient,
        private MapperBuilder $mapperBuilder,
    ) {}

    public function get(string $code): ?Antenne
    {
        try {
            $response = $this->httpClient->request('GET', '/api/antennes/' . $code, [
                'timeout' => 3,
            ]);
        } catch (TransportExceptionInterface) {
            return null;
        }

        try {
            $rawAntenne = $response->toArray();
        } catch (ClientExceptionInterface) {
            return null;
        }

        return $this->mapperBuilder
            ->allowSuperfluousKeys()
            ->supportDateFormats('Y-m-d H:i:s')
            ->mapper()
            ->map(Antenne::class, Source::array($rawAntenne)->camelCaseKeys());
    }
}
