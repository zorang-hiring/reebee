<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
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

    public function testCreateUser_notAuthorised()
    {
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(Auth::ID, new Auth(new UserRepositoryStub()));
        $app = new App($response = new Response(), $serviceContainer);
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => 'jon', 'password' => '123']);
        $app->dispatch($request);

        self::assertSame(401, $response->getStatus());
        self::assertSame([
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: No-Cache',
            'HTTP/1.0 401 Unauthorized'
        ], $response->buildHeaders());
        self::assertSame('', $response->getBody());
    }

    public function testCreate_invalidRequestParams()
    {
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(Auth::ID, new Auth(new UserRepositoryStub()));
        $serviceContainer->addServices(User::ID, new User(new UserRepositoryStub()));
        $app = new App(
            $response = new Response(),
            $serviceContainer,
            [
                Auth::APP_CREATE_USERS_TOKEN => 'createUsersToken'
            ]
        );
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => '', 'password' => '']);
        $request->setHeaders([
            'Authorization' => 'Basic createUsersToken'
        ]);
        $app->dispatch($request);

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

    public function testCreate_duplicatedUser()
    {
        self::markTestSkipped();
        $serviceContainer = new ServiceContainer();
        // $userRepository
        $serviceContainer->addServices(Auth::ID, new Auth(new UserRepositoryStub()));
        $serviceContainer->addServices(User::ID, new User(new UserRepositoryStub()));
        $app = new App(
            $response = new Response(),
            $serviceContainer,
            [
                Auth::APP_CREATE_USERS_TOKEN => 'createUsersToken'
            ]
        );
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => 'bob', 'password' => 'somePass']);
        $request->setHeaders([
            'Authorization' => 'Basic createUsersToken'
        ]);
        $app->dispatch($request);

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

    public function testCreate_success()
    {
        $serviceContainer = new ServiceContainer();
        $userRepository = new UserRepositoryStub();
        $serviceContainer->addServices(Auth::ID, new Auth($userRepository));
        $serviceContainer->addServices(User::ID, new User($userRepository));
        $app = new App(
            $response = new Response(),
            $serviceContainer,
            [
                Auth::APP_CREATE_USERS_TOKEN => 'createUsersToken'
            ]
        );
        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
        $request->setPostData(['username' => 'bob', 'password' => 'somePassword']);
        $request->setHeaders([
            'Authorization' => 'Basic createUsersToken'
        ]);
        $app->dispatch($request);

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

    function getProtectedProperty($obj, $prop) {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}