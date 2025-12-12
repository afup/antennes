<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\String\s;

#[AsController]
final readonly class ArchivesController
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private string $env,
    ) {}

    #[Route(
        path: '/{year}/{month<\d+>}/{day}/{slug}',
        requirements: [
            'year' => '[0-9]{4}',
            'month' => '[0-9]{2}',
            'day' => '[0-9]{2}',
            'slug' => '.+',
        ],
        host: '{code}.afup.org',
        priority: 10
    )]
    #[Route(
        path: '/{code}/{year}/{month}/{day}/{slug}',
        requirements: [
            'year' => '[0-9]{4}',
            'month' => '[0-9]{2}',
            'day' => '[0-9]{2}',
            'slug' => '.+',
        ],
        priority: 10,
        env: 'dev',
    )]
    public function article(string $code, string $year, string $month, string $day, string $slug): RedirectResponse
    {
        $slug = s($slug)->ensureEnd('/')->ensureEnd('index.html');
        $path = implode('/', [$year, $month, $day, $slug]);

        if ($this->env === 'dev') {
            $path = $code . '/' . $path;
        }

        return new RedirectResponse(
            '/archives/' . $path,
            Response::HTTP_MOVED_PERMANENTLY,
        );
    }

    #[Route(
        path: '/{path}.html',
        host: 'paris.afup.org',
        priority: 10
    )]
    #[Route(
        path: '/paris/{path}.html',
        priority: 10,
        env: 'dev',
    )]
    public function paris(string $path): RedirectResponse
    {
        if ($this->env === 'dev') {
            $path = 'paris/' . $path;
        }

        // Paris est la seule ville où les articles ne sont pas rangés par date, mais tous à la racine
        return new RedirectResponse('/archives/' . $path . '.html', Response::HTTP_MOVED_PERMANENTLY);
    }
}
