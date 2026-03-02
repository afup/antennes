<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Antenne;
use App\Dto\Meetup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/historique/{year?}',
    name: 'historique',
    host: '{code}.afup.org',
)]
#[Route(
    path: '/{code?}/historique/{year?}',
    name: 'historique',
    env: 'dev',
)]
final class HistoriqueAction extends AbstractController
{
    public function __invoke(Antenne $antenne, int|string|null $year): Response
    {
        if ($year === null) {
            $year = (int) date('Y');
        } elseif ($year !== 'anciens') {
            $year = (int) $year;
        }

        $selectedMeetups = [];

        $years = [];
        foreach ($antenne->meetups as $meetup) {
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
                $antenne->meetups,
                fn(Meetup $m) => in_array((int) $m->date->format('Y'), $olderYears),
            );
        }

        usort($selectedMeetups, fn(Meetup $a, Meetup $b) => $b->date <=> $a->date);

        return $this->render('historique.html.twig', [
            'antenne' => $antenne,
            'selectedYear' => $year,
            'years' => $recentYears,
            'selectedMeetups' => $selectedMeetups,
        ]);
    }
}
