<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\App;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use App\Service\Auth;
use App\Request;
use App\Service\User;
use App\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Stub\Repository\DbConnectorStub;
use Tests\Integration\Stub\Repository\UserRepositoryStub;

class UserTest extends TestCase
{
    /**
     * Test create user - not authorised request
     */
    public function testCreateUser_notAuthorised()
    {
        // GIVEN
        $app = $this->initApplication(new UserRepositoryStub(), []);

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/users');
        $request->setData(['username' => 'bob', 'password' => '123']);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(401, $response->getStatus());
        self::assertSame([
            'status' => 'ERROR',
            'errors' => 'Client is not authorised.'
        ], json_decode($response->getBody(), true));
    }

    /**
     * Test create user - invalid authorised request
     */
    public function testCreate_invalidRequestParams()
    {
        // GIVEN
        $app = $this->initApplication(
            new UserRepositoryStub(),
            // allowed API token to create user
            ['envVariables' => [Auth::APP_CREATE_USERS_TOKEN => 'some-api-token']]
        );

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/users');
        $request->setData(['username' => '', 'password' => '']);
        $request->setHeaders([
            // authorise request with Header:
            'Authorization' => 'Basic some-api-token'
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame(json_encode([
            'status' => 'ERROR',
            'errors' => [
                'username' => ['Has to be between 0 and 255 characters.'],
                'password' => ['Has to be between 3 and 20 characters.']
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
            // allowed API token to create user
            ['envVariables' => [Auth::APP_CREATE_USERS_TOKEN => 'some-api-token']]
        );

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/users');
        $request->setData(['username' => 'bob', 'password' => 'somePass']);
        $request->setHeaders([
            // authorise request with Header:
            'Authorization' => 'Basic some-api-token'
        ]);
        $response = $app->dispatch($request);

        // THEN
        self::assertSame(400, $response->getStatus());
        self::assertSame(json_encode([
            'status' => 'ERROR',
            'errors' => [
                'username' => ['Username already exists.']
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
            // allowed API token to create user
            ['envVariables' => [Auth::APP_CREATE_USERS_TOKEN => 'some-api-token']]
        );

        // WHEN
        $request = new Request(Request::METHOD_POST,  '/users');
        $request->setData(['username' => 'bob', 'password' => 'somePassword']);
        $request->setHeaders([
            // authorise request with Header:
            'Authorization' => 'Basic some-api-token'
        ]);
        $response = $app->dispatch($request);

        // THEN
        $savedDbData = $userRepository->getSavedData();
        self::assertCount(1, $savedDbData);
        self::assertSame('bob', $savedDbData[0]->getUsername());
        $savedPassword = $this->getProtectedProperty($savedDbData[0], 'password');
        self::assertNotEmpty($savedPassword, 'password should be randomly encrypted');
        self::assertNotSame('somePassword', $savedPassword, 'password should be encrypted');
        self::assertSame(200, $response->getStatus());
        self::assertSame(json_encode([
            'status' => 'OK',
            'data' => [
                'username' => 'bob'
            ]
        ]), $response->getBody());
    }

    protected function initApplication(UserRepositoryInterface $userRepository, array $options = [])
    {
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

    protected function getProtectedProperty($obj, $prop) {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}