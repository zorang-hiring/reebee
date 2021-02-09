<?php

//- Flyers should be retrievable by requesting all valid flyers or by flyerID
//- Anyone should be able to perform the Read operation for flyers
//- Require a User to use Basic Authentication to access the Create, Update, and Delete operations


use App\App;
use App\Repository\FlyerRepository;
use App\Repository\FlyerRepositoryInterface;
use App\Request;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;

class FlyerTest extends TestCase
{
    const BASE_URL = 'http://some.com';

    public function setUp()
    {
        parent::setUp();

        // mock app current time
//        $knownDate = \Carbon\Carbon::create(2000, 5, 6);
//        \Carbon\Carbon::setTestNow($knownDate);
    }

    /**
     * Test that anyone can get all flyers
     */
    public function testGetAll()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findAllValid'])
            ->getMock();
        $flyerRepository->expects(self::once())
            ->method('findAllValid')
            ->willReturn([
                (new \App\Entity\Flyer())
                    ->setFlyerID(1)
                    ->setName(2)
                    ->setStoreName(3)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2001-01-01 00:00:00'))
                    ->setPageCount(4),
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setName(6)
                    ->setStoreName(7)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-02-02 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2001-02-02 00:00:00'))
                    ->setPageCount(8)
            ]);
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET, self::BASE_URL . '/flyers');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            [
                'flyerID' => 1,
                'name' => '2',
                'storeName' => '3',
                'dateValid' => '2000-01-01',
                'dateExpired' => '2001-01-01',
                'pageCount' => 4,
            ],
            [
                'flyerID' => 5,
                'name' => '6',
                'storeName' => '7',
                'dateValid' => '2000-02-02',
                'dateExpired' => '2001-02-02',
                'pageCount' => 8,
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test get flyer by id (anyone)
     */
    public function testGetOneById()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $flyerRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setName(6)
                    ->setStoreName(7)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2001-01-01 00:00:00'))
                    ->setPageCount(8)
            );
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET, self::BASE_URL . '/flyers/5');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            'flyerID' => 5,
            'name' => '6',
            'storeName' => '7',
            'dateValid' => '2000-01-01',
            'dateExpired' => '2001-01-01',
            'pageCount' => 8,
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test that fayer can not be created by unauthenticated user
     */
    public function testCreate_noAuth()
    {
        // GIVEN

        // WHEN

        // THEN
        // self::assertSame(403, $response->getStatus());
    }

    /**
     * Test successful fayer creation
     */
    public function testCreate_auth()
    {
        // GIVEN

        // WHEN

        // THEN
        // self::assertSame(201, $response->getStatus());
    }

    /**
     * Test that fayer can not be updated by unauthenticated user
     */
    public function testUpdate_noAuth()
    {
        // GIVEN

        // WHEN

        // THEN
        // self::assertSame(403, $response->getStatus());
    }

    /**
     * Test successful fayer update
     */
    public function testUpdate_auth()
    {
        // GIVEN

        // WHEN

        // THEN
        // self::assertSame(204, $response->getStatus());
    }

    /**
     * Test that fayer can not be deleted by unauthenticated user
     */
    public function testDelete_noAuth()
    {
        // GIVEN

        // WHEN

        // THEN
        // self::assertSame(403, $response->getStatus());
    }

    /**
     * Test successful fayer delete
     */
    public function testDelete_auth()
    {
        // GIVEN

        // WHEN

        // THEN
        // self::assertSame(204, $response->getStatus());
    }

    protected function initApplication(FlyerRepositoryInterface $flyerRepository, array $options = [])
    {
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(\App\Service\Flyer::ID, new \App\Service\Flyer($flyerRepository));
        $evnVariables = !empty($options['envVariables']) ? $options['envVariables'] : [];
        $app = new App(
            $serviceContainer,
            $evnVariables
        );
        return $app;
    }
}