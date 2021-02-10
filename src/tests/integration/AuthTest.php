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

class AuthTest extends TestCase
{
    /**
     * Test auth wrong credentials
     */
    public function test_wrongCredentials()
    {
        // GIVEN
        $app = $this->initApplication(new UserRepositoryStub(), []);

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/auth');
        $request->setData(['username' => 'jon', 'password' => '123']);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame(json_encode([
            'status' => 'ERROR',
            'errors' => ['Wrong credentials provided.']
        ]), $response->getBody());
    }

    /**
     * Test auth right credentials
     */
    public function testCreate_validCredentials()
    {
        // GIVEN
        $userRepository = self::getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();
        $userRepository
            ->expects(self::atLeastOnce())
            ->method('findOneBy')
            ->willReturn(new \App\Entity\User('bob'));
        $app = $this->initApplication($userRepository, []);

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/auth');
        $request->setData(['username' => 'bob', 'password' => '123']);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(200, $response->getStatus());
        self::assertSame(json_encode([
            'status' => 'OK',
            'token' => base64_encode('bob:123')
        ]), $response->getBody());
    }

    protected function initApplication(
        UserRepositoryInterface $userRepository,
        array $options = []
    ){
        $serviceContainer = new ServiceContainer();
        $serviceContainer->addServices(Auth::ID, new Auth($userRepository));
        $serviceContainer->addServices(User::ID, new User($userRepository));
        $evnVariables = !empty($options['envVariables']) ? $options['envVariables'] : [];
        $app = new App(
            $serviceContainer,
            $evnVariables
        );
        return $app;
    }
}