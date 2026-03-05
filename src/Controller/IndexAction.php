<?php

declare(strict_types=1);

namespace App\Controller;

use App\ValueResolver\Tenant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'home',
    host: '{subdomain}.afup.org',
)]
#[Route(
    path: '/{subdomain?}',
    name: 'home',
    env: 'dev',
)]
final class IndexAction extends AbstractController
{
    public function __construct(
        #[Autowire('%afup.global.menu.event.label%')]
        private readonly string $currentEventName,
    ) {}

    public function __invoke(Tenant $tenant): Response
    {
        return $this->render('index.html.twig', [
            'tenant' => $tenant,
            'currentEvent' => $this->currentEventName,
        ]);
    }
}
