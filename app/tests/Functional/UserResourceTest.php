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

}