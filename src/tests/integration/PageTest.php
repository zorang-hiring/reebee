<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Entity\Flyer;
use App\Entity\Page;
use App\Entity\User;
use App\Repository\FlyerRepository;
use App\Repository\FlyerRepositoryInterface;
use App\Repository\PageRepository;
use App\Repository\PageRepositoryInterface;
use App\Request;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Stub\Repository\UserRepositoryStub;

//- Pages should be retrievable by requesting all pages for a flyerID or by pageID
//- Anyone should be able to perform the Read operation for pages
//- Require a User to use Basic Authentication to access the Create, Update, and Delete operations


class PageTest extends AbstractTestCase
{
    const EXISTING_USER_NAME = 'some-existed-user';

    /**
     * Test get All (anyone) - flyer not found
     */
    public function testGetAll_notFound()
    {
        // GIVEN
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOne'])
            ->getMock();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->willReturn(null);
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET,  '/flyers/5/pages');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => ['Flyer not found.']
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test get All (anyone) - flyer found
     */
    public function testGetAll_found()
    {
        // GIVEN
        $foundFlyer = self::getMockBuilder(\App\Entity\Flyer::class)
            ->setMethods(['getPages'])
            ->getMock();
        $foundFlyer->expects(self::atLeastOnce())
            ->method('getPages')
            ->willReturn([
                (new \App\Entity\Page())
                    ->setPageID(1)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2001-01-01 00:00:00'))
                    ->setPageNumber(4),
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-02-02 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2001-02-02 00:00:00'))
                    ->setPageNumber(8)
            ]);

        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOne'])
            ->getMock();
        $flyerRepository->expects(self::once())
            ->method('findOne')
            ->willReturn($foundFlyer);
        $app = $this->initApplication($flyerRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET,  '/flyers/5/pages');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            'status' => 'OK',
            'data' => [
                [
                    'pageID' => 1,
                    'dateValid' => '2000-01-01',
                    'dateExpired' => '2001-01-01',
                    'pageNumber' => 4,
                ],
                [
                    'pageID' => 5,
                    'dateValid' => '2000-02-02',
                    'dateExpired' => '2001-02-02',
                    'pageNumber' => 8,
                ]
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test get one page (anyone) - not found
     */
    public function testGetOne_notFound()
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $pageRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn(null);
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET,  '/pages/5');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => ['Page not found.']
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test get one page (anyone) - found
     */
    public function testGetOneById()
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $pageRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2001-01-01 00:00:00'))
                    ->setPageNumber(8)
            );
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_GET,  '/pages/5');
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            'status' => 'OK',
            'data' => [
                'pageID' => 5,
                'dateValid' => '2000-01-01',
                'dateExpired' => '2001-01-01',
                'pageNumber' => 8,
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test that page can not be created by unauthenticated user
     */
    public function testCreate_noAuth()
    {
        $this->_testNoAuth(
            new Request(Request::METHOD_POST,  '/pages')
        );
    }

    public function dataProvider_testCreate_invalidRequestParams()
    {
        return [
            [
                [],
                [
                    'flyerID' => ['Field is required.'],
                    'dateValid' => ['Field is required.', 'Has to be date in the form YYYY-MM-DD.'],
                    'dateExpired' => ['Field is required.', 'Has to be date in the form YYYY-MM-DD.'],
                ]
            ],
            [
                [
                    'flyerID' => 1,
                    'dateValid' => 'b',
                    'dateExpired' => 'c',
                ],
                [
                    'dateValid' => ['Has to be date in the form YYYY-MM-DD.'],
                    'dateExpired' => ['Has to be date in the form YYYY-MM-DD.'],
                    'flyerID' => ['Flyer "1" does not exist.']
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
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::never())->method('save');
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $app = $this->initApplication($flyerRepository, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/pages');
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
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::once())
            ->method('save');
        $flyerRepository = self::getMockBuilder(FlyerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOne'])
            ->getMockForAbstractClass();
        $flyerRepository->expects(self::atLeastOnce())
            ->method('findOne')
            ->with(6)
            ->willReturn((new Flyer())->setFlyerID(6));
        $app = $this->initApplication($flyerRepository, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/pages');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData([
            'flyerID' => '6',
            'dateValid' => '2000-01-01',
            'dateExpired' => '2001-01-01'
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            'status' => 'OK',
            'data' => [
                'pageID' => null, // not updated because test mocks repository save method
                'dateValid' => '2000-01-01',
                'dateExpired' => '2001-01-01',
                'pageNumber' => null
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test that page can not be updated by unauthenticated user
     */
    public function testUpdate_noAuth()
    {
        $this->_testNoAuth(
            new Request(Request::METHOD_PATCH,  '/pages/5')
        );
    }

    /**
     * Test update for non existed page
     */
    public function testUpdate_noPage()
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findOne'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::once())
            ->method('findOne')
            ->with(3)
            ->willReturn(null);
        $pageRepository->expects(self::never())->method('save');
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_PATCH,  '/pages/3');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData([
            'flyerID' => '6',
            'dateValid' => '2000-01-01',
            'dateExpired' => '2001-01-01',
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => 'no such page'
        ], json_decode($response->getBody(), true));
    }

    public function dataProvider_testUpdate_invalidRequestParams()
    {
        return [
            [
                [
                    'dateValid' => 'b',
                    'dateExpired' => 'c',
                ],
                [
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
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findOne'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::once())
            ->method('findOne')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            );
        $pageRepository->expects(self::never())->method('save');
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_PATCH,  '/pages/5');
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
     * Test successful page update
     */
    public function testUpdate_success()
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'findOne'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::once())
            ->method('findOne')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
                    ->setPageNumber(7)
            );
        $pageRepository->expects(self::once())
            ->method('save')
            ->with(
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-02 00:00:00'))
                    ->setPageNumber(7)
            )
        ;
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_PATCH,  '/pages/5');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $request->setData([
            'dateValid' => '2010-01-01',
            'dateExpired' => '2010-01-02',
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame(['status' => 'OK', 'message' => 'Item updated.'], json_decode($response->getBody(), true));
    }

    /**
     * Test that page can not be deleted by unauthenticated user
     */
    public function testDelete_noAuth()
    {
        $this->_testNoAuth(
            new Request(Request::METHOD_DELETE,  '/pages/5')
        );
    }

    /**
     * Test delete for non existed page
     */
    public function testDelete_noPage()
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['remove', 'findOne'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::once())
            ->method('findOne')
            ->with(3)
            ->willReturn(null);
        $pageRepository->expects(self::never())->method('remove');
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_DELETE,  '/pages/3');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => 'No such page.'
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test successful page delete
     */
    public function testDelete_success()
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['remove', 'findOne'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::once())
            ->method('findOne')
            ->with(5)
            ->willReturn(
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            );
        $pageRepository->expects(self::once())
            ->method('remove')
            ->with(
                (new \App\Entity\Page())
                    ->setPageID(5)
                    ->setDateValid(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'))
                    ->setDateExpired(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-02 00:00:00'))
            )
        ;
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $request = new Request(Request::METHOD_DELETE,  '/pages/5');
        $this->addBasicAuthHeader($request, ['user' => self::EXISTING_USER_NAME]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame(['status' => 'OK', 'message' => 'Item deleted.'], json_decode($response->getBody(), true));
    }

    protected function _testNoAuth(Request $request)
    {
        // GIVEN
        $pageRepository = self::getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'remove'])
            ->getMockForAbstractClass();
        $pageRepository->expects(self::never())->method('save');
        $pageRepository->expects(self::never())->method('remove');
        $app = $this->initApplication(null, $pageRepository);

        // WHEN
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(403, $response->getStatus());
    }

    protected function initApplication(
        FlyerRepositoryInterface $flyerRepository = null,
        PageRepositoryInterface $pageRepository = null,
        array $options = []
    ){
        $serviceContainer = new ServiceContainer();
        if ($flyerRepository) {
            $serviceContainer->addServices(\App\Service\Flyer::ID, new \App\Service\Flyer($flyerRepository));
        }
        if ($pageRepository) {
            $serviceContainer->addServices(\App\Service\Page::ID, new \App\Service\Page($pageRepository));
        }

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
}