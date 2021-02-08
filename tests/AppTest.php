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
        self::assertSame([],  $response->getHeaders());
        self::assertSame('',  $response->getBody());
    }

    public function testAppGetRequestDispatch()
    {
        $app = new App($response = new Response());
        $app->dispatch(new Request(self::URL . '/flyers'));

        self::assertSame(200,  $response->getStatus());
        self::assertSame([],  $response->getHeaders());
        self::assertSame('[]',  $response->getBody());
    }
}