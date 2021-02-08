<?php
declare(strict_types=1);
namespace Tests;

use App\App;
use App\Request;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    public function testAppExists()
    {
        $request = new Request();

        $app = new App();
        $app->run($request);
    }
}