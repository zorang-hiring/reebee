<?php
declare(strict_types=1);
namespace Tests\Unit;

use App\App;
use App\Request;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    const BASE_URL = 'http://some.com';

    public function testAppEmptyRequest_response()
    {
        $app = new App(new ServiceContainer());
        $response = $app->dispatch(new Request(Request::METHOD_GET, self::BASE_URL));

        self::assertSame(404,  $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 404 Not Found'
        ],  $response->buildHeaders());
        self::assertSame('',  $response->getBody());
    }

    public function testAppGetFlyersRequest_response()
    {
        $app = new App( new ServiceContainer());
        $response = $app->dispatch(new Request(Request::METHOD_GET, self::BASE_URL . '/flyers'));

        self::assertSame(200,  $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 200 OK'

        ],  $response->buildHeaders());
        self::assertSame('[]',  $response->getBody());
    }
}