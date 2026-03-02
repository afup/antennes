<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Twig\Environment;

#[AsEventListener]
final readonly class CodeAntenneInvalideListener
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private string $env,
        private Environment $twig,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof CodeAntenneInvalideException) {
            return;
        }

        if ($this->env === 'dev') {
            $event->setResponse(new Response($this->twig->render('dev.html.twig')));

            return;
        }

        $event->setResponse(new RedirectResponse('https://afup.org'));
    }
}
