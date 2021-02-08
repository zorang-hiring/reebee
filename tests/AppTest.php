<?php
declare(strict_types=1);
namespace Tests;

use App\App;
use App\Presenter;
use App\Request;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    public function testAppExists()
    {
        $request = new Request();
        $presenter = new Presenter();

        $app = new App($presenter);
        $app->run($request);
    }
}