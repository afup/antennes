<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Meetup;
use App\ValueResolver\Tenant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/meetups/{year?}',
    name: 'meetups',
    host: '{subdomain}.afup.org',
)]
#[Route(
    path: '/{subdomain?}/meetups/{year?}',
    name: 'meetups',
    env: 'dev',
)]
final class MeetupsAction extends AbstractController
{
    public function __invoke(Tenant $tenant, int|string|null $year): Response
    {
        if ($year === null) {
            $year = (int) date('Y');
        } elseif ($year !== 'anciens') {
            $year = (int) $year;
        }

        $selectedMeetups = [];

        $years = [];
        foreach ($tenant->antenne->meetups as $meetup) {
            $meetupYear = (int) $meetup->date->format('Y');

            $years[$meetupYear] = true;

            if (is_int($year) && $meetupYear === $year) {
                $selectedMeetups[] = $meetup;
            }
        }

        $years = array_keys($years);
        rsort($years);

        $recentYears = array_slice($years, 0, 10);
        $olderYears = array_slice($years, 10);

        if (count($olderYears) > 0) {
            $recentYears[] = 'anciens';
        }

        if ($year === 'anciens') {
            $selectedMeetups = array_filter(
                $tenant->antenne->meetups,
                fn(Meetup $m) => in_array((int) $m->date->format('Y'), $olderYears),
            );
        }

        // Gestion du cas où il n'y a pas encore de meetups pour l'année en cours
        if (is_int($year) && count($selectedMeetups) === 0) {
            $closestYearWithMeetups = 0;

            foreach ($tenant->antenne->meetups as $meetup) {
                $meetupYear = (int) $meetup->date->format('Y');

                if ($meetupYear > $closestYearWithMeetups) {
                    $closestYearWithMeetups = $meetupYear;
                }
            }

            $year = $closestYearWithMeetups;

            foreach ($tenant->antenne->meetups as $meetup) {
                $meetupYear = (int) $meetup->date->format('Y');

                if ($meetupYear === $year) {
                    $selectedMeetups[] = $meetup;
                }
            }
        }

        // Si malgré tout ça il n'y a toujours pas de meetup sélectionnés,
        // c'est qu'on est probablement arrivés sur une page directement.
        if (count($selectedMeetups) === 0) {
            return $this->redirectToRoute('home', [
                'subdomain' => $tenant->subdomain,
            ]);
        }

        usort($selectedMeetups, fn(Meetup $a, Meetup $b) => $b->date <=> $a->date);

        return $this->render('meetups.html.twig', [
            'tenant' => $tenant,
            'selectedYear' => $year,
            'years' => $recentYears,
            'selectedMeetups' => $selectedMeetups,
        ]);
    }
}
