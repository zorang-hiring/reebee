<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Entity\User;
use App\Repository\FlyerRepository;
use App\Repository\FlyerRepositoryInterface;
use App\Request;
use App\Service\Flyer;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Stub\Repository\UserRepositoryStub;

class FlyerTest extends TestCase
{
    const BASE_URL = 'http://some.com';

    const EXISTING_USER_NAME = 'some-existed-user';

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
            'status' => 'OK',
            'data' => [
                [
                    'flyerID' => 1,
                    'name' => 2,
                    'storeName' => 3,
                    'dateValid' => '2000-01-01',
                    'dateExpired' => '2001-01-01',
                    'pageCount' => 4,
                ],
                [
                    'flyerID' => 5,
                    'name' => 6,
                    'storeName' => 7,
                    'dateValid' => '2000-02-02',
                    'dateExpired' => '2001-02-02',
                    'pageCount' => 8,
                ]
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test get one flyer (anyone) - not found
     */
    public function testGetOne_notFound()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $flyerRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn(null);
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET, self::BASE_URL . '/flyers/5');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => ['Flyer not found.']
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test get one flyer (anyone) - found
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
            'status' => 'OK',
            'data' => [
                'flyerID' => 5,
                'name' => 6,
                'storeName' => 7,
                'dateValid' => '2000-01-01',
                'dateExpired' => '2001-01-01',
                'pageCount' => 8,
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test that fayer can not be created by unauthenticated user
     */
    public function testCreate_noAuth()
    {
        $this->_testNoAuth(
            new Request(Request::METHOD_POST, self::BASE_URL . '/flyers')
        );
    }

    public function dataProvider_testCreate_invalidRequestParams()
    {
        return [
            [
                [],
                [
                    'name' => ['Field is required.'],
                    'storeName' => ['Field is required.'],
                    'dateValid' => ['Field is required.', 'Has to be date in the form YYYY-MM-DD.'],
                    'dateExpired' => ['Field is required.', 'Has to be date in the form YYYY-MM-DD.'],
                    'pageCount' => ['Field is required.']
                ]
            ],
            [
                [
                    'name' => 1,
                    'storeName' => 2,
                    'dateValid' => 'b',
                    'dateExpired' => 'c',
                    'pageCount' => 'd'
                ],
                [
                    'pageCount' => ['Has to be integer.'],
                    'dateValid' => ['Has to be date in the form YYYY-MM-DD.'],
                    'dateExpired' => ['Has to be date in the form YYYY-MM-DD.'],
                ]
            ]
        ];
    }

    /**
     * Test create invalid request params
     *
     * @dataProvider dataProvider_testCreate_invalidRequestParams
     */
    public function testCreate_invalidRequestParams($postData, $expectedValidationErrors)
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::never())->method('save');
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/flyers');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData($postData);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => $expectedValidationErrors
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test successful fayer creation
     */
    public function testCreate_success()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::once())->method('save');
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/flyers');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData([
            'name' => '6',
            'storeName' => '7',
            'dateValid' => '2000-01-01',
            'dateExpired' => '2001-01-01',
            'pageCount' => 8,
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(201, $response->getStatus());
        self::assertSame([
            'status' => 'OK',
            'data' => [
                'flyerID' => null, // not updated because test mocks repository save method
                'name' => '6',
                'storeName' => '7',
                'dateValid' => '2000-01-01',
                'dateExpired' => '2001-01-01',
                'pageCount' => 8,
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test that fayer can not be updated by unauthenticated user
     */
    public function testUpdate_noAuth()
    {
        $this->_testNoAuth(
            new Request(Request::METHOD_PATCH, self::BASE_URL . '/flyers/5')
        );
    }

    /**
     * Test update for non existed flyer
     */
    public function testUpdate_noFlyer()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findOne'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->with(3)
            ->willReturn(null);
        $flyerRepository->expects(self::never())->method('save');
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_PATCH, self::BASE_URL . '/flyers/3');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData([
            'name' => '6',
            'storeName' => '7',
            'dateValid' => '2000-01-01',
            'dateExpired' => '2001-01-01',
            'pageCount' => 8,
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => 'no such flyer'
        ], json_decode($response->getBody(), true));
    }

    public function dataProvider_testUpdate_invalidRequestParams()
    {
        return [
            [
                [
                    'name' => '',
                ],
                [
                    'name' => ['Field can not be empty.']
                ]
            ],
            [
                [
                    'storeName' => '',
                    'dateValid' => 'b',
                    'dateExpired' => 'c',
                    'pageCount' => 'd'
                ],
                [
                    'storeName' => ['Field can not be empty.'],
                    'pageCount' => ['Has to be integer.'],
                    'dateValid' => ['Has to be date in the form YYYY-MM-DD.'],
                    'dateExpired' => ['Has to be date in the form YYYY-MM-DD.'],
                ]
            ]
        ];
    }

    /**
     * Test update invalid request params
     *
     * @dataProvider dataProvider_testUpdate_invalidRequestParams
     */
    public function testUpdate_invalidRequestParams($postData, $expectedValidationErrors)
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findOne'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setPageCount(41)
                    ->setStoreName('old storeName')
                    ->setName('old name')
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            );
        $flyerRepository->expects(self::never())->method('save');
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_PATCH, self::BASE_URL . '/flyers/5');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData($postData);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => $expectedValidationErrors
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test successful fayer update
     */
    public function testUpdate_success()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findOne'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setPageCount(41)
                    ->setStoreName('old storeName')
                    ->setName('old name')
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            );
        $flyerRepository->expects(self::once())
            ->method('save')
            ->with(
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setPageCount(42)
                    ->setStoreName('new storeName')
                    ->setName('new name')
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-02 00:00:00'))
            )
        ;
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_PATCH, self::BASE_URL . '/flyers/5');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData([
            'pageCount' => 42,
            'storeName' => 'new storeName',
            'name' => 'new name',
            'dateValid' => '2010-01-01',
            'dateExpired' => '2010-01-02',
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(204, $response->getStatus());
        self::assertSame(['status' => 'OK', 'message' => 'Item updated.'], json_decode($response->getBody(), true));
    }

    /**
     * Test that fayer can not be deleted by unauthenticated user
     */
    public function testDelete_noAuth()
    {
        $this->_testNoAuth(
            new Request(Request::METHOD_DELETE, self::BASE_URL . '/flyers/5')
        );
    }

    /**
     * Test delete for non existed flyer
     */
    public function testDelete_noFlyer()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['remove', 'findOne'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->with(3)
            ->willReturn(null);
        $flyerRepository->expects(self::never())->method('remove');
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_DELETE, self::BASE_URL . '/flyers/3');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => 'No such flyer.'
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test successful fayer delete
     */
    public function testDelete_success()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['remove', 'findOne'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setPageCount(41)
                    ->setStoreName('storeName')
                    ->setName('name')
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            );
        $flyerRepository->expects(self::once())
            ->method('remove')
            ->with(
                (new \App\Entity\Flyer())
                    ->setFlyerID(5)
                    ->setPageCount(41)
                    ->setStoreName('storeName')
                    ->setName('name')
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            )
        ;
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_DELETE, self::BASE_URL . '/flyers/5');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(204, $response->getStatus());
        self::assertSame(['status' => 'OK', 'message' => 'Item deleted.'], json_decode($response->getBody(), true));
    }

    protected function _testNoAuth(Request $request)
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass();
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(403, $response->getStatus());
    }

    protected function initApplication(
        FlyerRepositoryInterface $flyerRepository,
        array $options = []
    ){
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(\App\Service\Flyer::ID, new \App\Service\Flyer($flyerRepository));

        // stub User repository with some existed users to be able to login with that user name
        $userRepository = new UserRepositoryStub();
        $userRepository->setFindUserByCredentialsData([new User(self::EXISTING_USER_NAME)]);
        $serviceContainer->addServices(\App\Service\Auth::ID, new \App\Service\Auth($userRepository));

        $evnVariables = !empty($options['envVariables']) ? $options['envVariables'] : [];
        $app = new App(
            $serviceContainer,
            $evnVariables
        );
        return $app;
    }

    protected function addBasicAuthHeader(Request $request, array $options)
    {
        $request->setHeaders(['Authorization' => 'Basic ' . base64_encode($options['user']. ':123')]);
    }
}