<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use App\Service\Auth;
use App\Request;
use App\Response;
use App\Service\User;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Stub\Repository\DbConnectorStub;
use Tests\Integration\Stub\Repository\UserRepositoryStub;

//- Allow for creation of Users using the API token: `secret-token`
//- Users should have at minimum a username and password to perform Basic Authentication

class UserTest extends TestCase
{
    const BASE_URL = 'http://some.com';

    /**
     * Test create user - not authorised request
     */
    public function testCreateUser_notAuthorised()
    {
        // GIVEN
        $app = $this->initApplication(new UserRepositoryStub(), $response = new Response(), []);

        // WHEN
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => 'jon', 'password' => '123']);
        $app->dispatch($request);

        // THEN
        self::assertSame(401, $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 401 Unauthorized'
        ], $response->buildHeaders());
        self::assertSame('', $response->getBody());
    }

    /**
     * Test create user - invalid authorised request
     */
    public function testCreate_invalidRequestParams()
    {
        // GIVEN
        $app = $this->initApplication(
            new UserRepositoryStub(),
            $response = new Response(),
            ['envVariables' => [Auth::APP_CREATE_USERS_TOKEN => 'createUsersToken']]
        );

        // WHEN
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => '', 'password' => '']);
        $request->setHeaders([
            'Authorization' => 'Basic createUsersToken'
        ]);
        $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 400 Bad Request'
        ], $response->buildHeaders());
        self::assertSame(json_encode([
            'errors' => [
                'username' => 'Has to be between 0 and 255 characters.',
                'password' => 'Has to be between 3 and 20 characters.'
            ]
        ]), $response->getBody());
    }

    /**
     * Test create user - duplicated username
     */
    public function testCreate_duplicatedUser()
    {
        // GIVEN
        /** @var UserRepository $userRepository */
        $userRepository = self::getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneByUsername', 'save'])
            ->getMock();
        $userRepository->expects(self::never())->method('save');
        $userRepository
            ->expects(self::atLeastOnce())
            ->method('findOneByUsername')
            ->with('bob')
            ->willReturn(true);
        $app = $this->initApplication(
            $userRepository,
            $response = new Response(),
            ['envVariables' => [Auth::APP_CREATE_USERS_TOKEN => 'createUsersToken']]
        );

        // WHEN
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => 'bob', 'password' => 'somePass']);
        $request->setHeaders([
            'Authorization' => 'Basic createUsersToken'
        ]);
        $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 400 Bad Request'
        ], $response->buildHeaders());
        self::assertSame(json_encode([
            'errors' => [
                'username' => 'Username already exists.'
            ]
        ]), $response->getBody());
    }

    /**
     * Test create user - successful
     */
    public function testCreate_success()
    {
        // GIVEN
        $userRepository = new UserRepositoryStub();
        $app = $this->initApplication(
            $userRepository,
            $response = new Response(),
            ['envVariables' => [Auth::APP_CREATE_USERS_TOKEN => 'createUsersToken']]
        );

        // WHEN
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => 'bob', 'password' => 'somePassword']);
        $request->setHeaders([
            'Authorization' => 'Basic createUsersToken'
        ]);
        $app->dispatch($request);

        // THEN
        $savedDbData = $userRepository->getSavedData();
        self::assertCount(1, $savedDbData);
        self::assertSame('bob', $savedDbData[0]->getUsername());
        $savedPassword = $this->getProtectedProperty($savedDbData[0], 'password');
        self::assertNotEmpty($savedPassword, 'password should be randomly encrypted');
        self::assertNotSame('somePassword', $savedPassword, 'password should be encrypted');
        self::assertSame(201, $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 201 Created'
        ], $response->buildHeaders());
        self::assertSame(json_encode(['username' => 'bob']), $response->getBody());
    }

    protected function initApplication(
        UserRepositoryInterface $userRepository,
        Response $response,
        array $options = []
    ){
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(Auth::ID, new Auth($userRepository));
        $serviceContainer->addServices(User::ID, new User($userRepository));
        $evnVariables = !empty($options['envVariables']) ? $options['envVariables'] : [];
        $app = new App(
            $response,
            $serviceContainer,
            $evnVariables
        );
        return $app;
    }

    protected function getProtectedProperty($obj, $prop) {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}