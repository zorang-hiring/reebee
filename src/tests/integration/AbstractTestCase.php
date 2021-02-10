<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Request;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function addBasicAuthHeader(Request $request, array $options)
    {
        $request->setHeaders(['Authorization' => 'Basic ' . base64_encode($options['user']. ':123')]);
    }

    protected function setUp()
    {
        parent::setUp();

        // mock doctrine entity manager to make possible to call entity reference
        /** @var MockObject $em */
        $em = self::getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getReference'])
            ->getMockForAbstractClass();
        $em->expects(self::any())->method('getReference')->willReturnCallback(function($class) {
            return new $class;
        });
        App::setEm($em);
    }
}