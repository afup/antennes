<?php

declare(strict_types=1);

namespace App\Seo;

use App\Dto\Meetup;
use App\Tenant\TenantFetcher;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Organization;
use Spatie\SchemaOrg\Schema;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class SchemaGenerator
{
    public function __construct(
        private RouterInterface $router,
        private RequestStack $requestStack,
        private Packages $packages,
        private TenantFetcher $tenantFetcher,
    ) {}

    public function organisation(): Organization
    {
        $tenant = $this->tenantFetcher->current();
        $logo = $this->packages->getUrl('logos/afup-antenne-' . $tenant->antenne->code . '.svg');
        $host = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost() ?? '';

        return Schema::organization()
            ->name('AFUP ' . $tenant->antenne->label)
            ->url(
                $this->router->generate(
                    'home',
                    ['subdomain' => $tenant->subdomain],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
            )
            ->logo($host . $logo);
    }

    public function nextEvent(): ?Event
    {
        $nextMeetup = $this->tenantFetcher->current()->antenne->nextMeetup;

        if (!$nextMeetup) {
            return null;
        }

        return $this->event($nextMeetup)
            ->organizer($this->organisation());
    }

    public function event(Meetup $meetup): Event
    {
        $schema = Schema::event()
            ->name($meetup->title)
            ->startDate($meetup->date)
            ->url($meetup->url)
        ;

        if ($meetup->photo) {
            $schema = $schema->image($meetup->photo);
        }

        if ($meetup->location !== null) {
            $schema = $schema->location($meetup->location);
        }

        return $schema;
    }

    /**
     * @param array<Meetup> $meetups
     */
    public function events(array $meetups): Organization
    {
        return $this->organisation()
            ->events(array_map(fn(Meetup $meetup) => $this->event($meetup), $meetups));
    }
}
