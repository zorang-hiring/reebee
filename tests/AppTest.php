<?php
declare(strict_types=1);
namespace Tests;

use App\App;
use App\Response;
use App\Request;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    public function testAppExists()
    {
        $request = new Request('');
        $presenter = new Response();

        $app = new App($presenter);
        $app->run($request);

        self::assertSame('hello',  $presenter->print());
    }
}