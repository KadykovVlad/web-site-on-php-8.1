<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testMatchesStaticRoute(): void
    {
        $router = new Router();
        $router->get('/', fn () => 'home');

        [$handler, $params] = $router->match('/');

        $this->assertSame('home', $handler());
        $this->assertSame([], $params);
    }

    public function testMatchesDynamicRouteAndExtractsParam(): void
    {
        $router = new Router();
        $router->get('/category/{slug}', fn () => 'category');

        $match = $router->match('/category/php');

        $this->assertNotNull($match);
        $this->assertSame(['slug' => 'php'], $match[1]);
    }

    public function testReturnsNullWhenNoRouteMatches(): void
    {
        $router = new Router();
        $router->get('/', fn () => 'home');

        $this->assertNull($router->match('/does-not-exist'));
    }

    public function testStaticRouteDoesNotMatchNestedPath(): void
    {
        $router = new Router();
        $router->get('/', fn () => 'home');

        $this->assertNull($router->match('/category/php'));
    }

    public function testIgnoresQueryStringAndTrailingSlash(): void
    {
        $router = new Router();
        $router->get('/category/{slug}', fn () => 'category');

        $match = $router->match('/category/php/?sort=views&page=2');

        $this->assertNotNull($match);
        $this->assertSame(['slug' => 'php'], $match[1]);
    }

    public function testFirstMatchingRouteWins(): void
    {
        $router = new Router();
        $router->get('/article/{slug}', fn () => 'first');
        $router->get('/article/{slug}', fn () => 'second');

        [$handler] = $router->match('/article/hello');

        $this->assertSame('first', $handler());
    }
}
