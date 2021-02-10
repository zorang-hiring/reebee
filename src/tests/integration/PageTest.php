<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Entity\User;
use App\Repository\FlyerRepository;
use App\Repository\FlyerRepositoryInterface;
use App\Repository\PageRepository;
use App\Request;
use App\Service\Flyer;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Stub\Repository\UserRepositoryStub;

//- Pages should be retrievable by requesting all pages for a flyerID or by pageID
//- Anyone should be able to perform the Read operation for pages
//- Require a User to use Basic Authentication to access the Create, Update, and Delete operations


class PageTest extends TestCase
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

    protected function initApplication(
        FlyerRepositoryInterface $flyerRepository,
        PageRepository $pageRepository = null,
        array $options = []
    ){
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(\App\Service\Flyer::ID, new \App\Service\Flyer($flyerRepository));
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