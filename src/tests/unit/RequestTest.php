<?php
declare(strict_types=1);
namespace Tests\Unit;

use App\App;
use App\Response;
use App\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testEmpty()
    {
        $request = new Request('', '');

        self::assertSame('', $request->getPath());
        self::assertSame(null, $request->getQuery());
    }

    public function testFilled()
    {
        $request = new Request(Request::METHOD_GET, '/a/b/?c=d');

        self::assertSame('/a/b/', $request->getPath());
        self::assertSame(['c' => 'd'], $request->getQuery());
    }
}