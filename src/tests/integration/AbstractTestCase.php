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

abstract class AbstractTestCase extends TestCase
{
    protected function addBasicAuthHeader(Request $request, array $options)
    {
        $request->setHeaders(['Authorization' => 'Basic ' . base64_encode($options['user']. ':123')]);
    }
}