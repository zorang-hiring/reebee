<?php
declare(strict_types=1);
namespace Tests;

use App\App;
use App\Response;
use App\Request;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    const URL = 'http://a.b.com';

    public function testAppEmptyRequestDispatch()
    {
        $app = new App($response = new Response());
        $app->dispatch(new Request(self::URL));

        self::assertSame(404,  $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 404 Not Found'
        ],  $response->buildHeaders());
        self::assertSame('',  $response->getBody());
    }

    public function testAppGetRequestDispatch()
    {
        $app = new App($response = new Response());
        $app->dispatch(new Request(self::URL . '/flyers'));

        self::assertSame(200,  $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache'
        ],  $response->buildHeaders());
        self::assertSame('[]',  $response->getBody());
    }
}