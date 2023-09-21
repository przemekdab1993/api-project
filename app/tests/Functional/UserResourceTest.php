<?php

namespace App\Tests\Functional;

use App\Entity\UserApi;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;


    public function  testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/user_apis', [
            'json' => [
                'email' => 'kreda@example.com',
                'userName' => 'kreda',
                'password' => 'kreda'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->userLogin($client, 'kreda@example.com', 'kreda');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();
        $user = $this->createAndLoginUser($client, 'duda@example.com', 'Andrzej');

        $client->request('PUT', '/api/user_apis/'.$user->getId(), [
            'json' => [
                'userName' => 'Jarosław',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'userName' => 'Jarosław'
        ]);
    }

}