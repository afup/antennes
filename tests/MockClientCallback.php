<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class MockClientCallback
{
    /**
     * @param array<mixed> $options
     */
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        if ($url === 'https://afup.org/api/antennes/lyon') {
            return new JsonMockResponse([
                'code' => 'lyon',
                'label' => 'Lyon',
                'logo' => [
                    'simple' => 'https://afup.example/logo/lyon.svg',
                ],
                'links' => [
                    'meetup' => 'https://meetup.example/lyon',
                    'linkedin' => 'https://linkedin.example/lyon',
                    'bluesky' => 'https://bluesky.example/lyon',
                ],
            ]);
        }

        if ($url === 'https://afup.org/api/antennes/bordeaux') {
            return new JsonMockResponse([
                'code' => 'bordeaux',
                'label' => 'Bordeaux',
                'logo' => [
                    'simple' => 'https://afup.example/logo/bordeaux.svg',
                ],
                'links' => [
                    'meetup' => 'https://meetup.example/bordeaux',
                    'linkedin' => null,
                    'bluesky' => null,
                ],
            ]);
        }

        if (str_starts_with($url, 'https://afup.org/api/antennes/')) {
            return new MockResponse(info: ['http_code' => 404]);
        }

        return new MockResponse(info: ['http_code' => 500]);
    }
}
