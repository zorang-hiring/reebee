<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Service\Auth;
use App\Request;
use App\Response;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Stub\Repository\UserRepositoryStub;


//- Allow for creation of Users using the API token: `secret-token`
//- Users should have at minimum a username and password to perform Basic Authentication

class UserTest extends TestCase
{
    const BASE_URL = 'http://some.com';

    public function testCreate_notAuthorised()
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

//    public function testCreate_invalidRequestParams()
//    {
//        $serviceContainer = new ServiceContainer();
//        $serviceContainer->addServices(Authentication::ID, new Authentication());
//        $app = new App($response = new Response(), $serviceContainer);
//        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
//        $request->setPostData(['username' => 'jon', 'password' => '123']);
//        $request->setHeaders([]);
//        $app->dispatch($request);
//
//        self::assertSame(400, $response->getStatus());
//        self::assertSame([
//            'Content-Type: application/json',
//            'Accept: application/json',
//            'Cache-Control: No-Cache',
//            'HTTP/1.0 400 Bad Request'
//        ], $response->buildHeaders());
//        self::assertSame(json_encode([
//            'errors' => [
//                [
//                    'field' => 'username',
//                    'messages' => [
//                        'username should not be empty'
//                    ]
//                ],
//                [
//                    'field' => 'password',
//                    'messages' => [
//                        'username should not be empty'
//                    ]
//                ]
//            ]
//        ]), $response->getBody());
//    }

//    public function testCreate_duplicatedUser()
//    {
//
//    }
//
//    public function testCreate_success()
//    {
//        $response = new Response();
//        $app = new App($response);
//        $request = new Request(Request::METHOD_POST, self::BASE_URL . '/users');
//        $request->setPostData(['username' => 'jon', 'password' => '123']);
//        $app->dispatch($request);
//
//        self::assertSame(201, $response->getStatus());
//        self::assertSame([
//            'Content-Type: application/json',
//            'Accept: application/json',
//            'Cache-Control: No-Cache',
//            'HTTP/1.0 201 Created'
//        ], $response->buildHeaders());
//        self::assertSame('', $response->getBody());
//    }

      protected function initApp()
      {

      }
}