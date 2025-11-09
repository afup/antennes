<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\AntennesRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    host: '{code}.afup.org',
)]
#[Route(
    path: '/{code}',
    env: 'dev',
)]
final class IndexAction extends AbstractController
{
    public function __construct(
        #[Autowire('%afup.global.menu.event.label%')]
        private readonly string $currentEventName,
        #[Autowire('%kernel.environment%')]
        private readonly string $env,
        private readonly AntennesRepository $antennesRepository,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(string $code): Response
    {
        $antenne = $this->antennesRepository->get($code);
        if ($antenne === null) {
            return $this->error("Code `$code` is invalid");
        }

        return $this->render('index.html.twig', [
            'antenne' => $antenne,
            'currentEvent' => $this->currentEventName,
        ]);
    }

    private function error(string $message): RedirectResponse
    {
        if ($this->env === 'dev') {
            throw $this->createNotFoundException($message);
        }

        $this->logger->error($message);

        return $this->redirect('https://afup.org');
    }
}
