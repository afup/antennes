<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AntenneTest extends WebTestCase
{
    private function callWithAntenne(string $code): void
    {
        $client = self::createClient();
        $client->request('GET', '/', server: ['HTTP_HOST' => $code . '.afup.org']);
    }

    public function test_with_invalid_antenne_code(): void
    {
        $this->callWithAntenne('invalid');

        self::assertResponseRedirects('https://afup.org');
    }

    public function test_with_valid_and_complete_antenne_code(): void
    {
        $this->callWithAntenne('lyon');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('a[href="https://meetup.example/lyon"]');
        self::assertSelectorExists('a[href="https://linkedin.example/lyon"]');
        self::assertSelectorExists('a[href="https://bluesky.example/lyon"]');

        self::assertSelectorTextContains('[data-qa="current-event"]', 'PHP Tour 4242');
    }

    public function test_with_valid_and_incomplete_antenne_code(): void
    {
        $this->callWithAntenne('bordeaux');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('a[href="https://meetup.example/bordeaux"]');
        self::assertSelectorNotExists('a[href="https://linkedin.example/bordeaux"]');
        self::assertSelectorNotExists('a[href="https://bluesky.example/bordeaux"]');

        self::assertSelectorTextContains('[data-qa="current-event"]', 'PHP Tour 4242');
    }
}
