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
                'roles' => ['ROLE_ADMIN']
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'userName' => 'Jarosław'
        ]);

        $em = $this->getEntityManager();

        /**
         * @var $user UserApi
         */
        $user = $em->getRepository(UserApi::class)->find($user->getId());

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetUser()
    {
        $client = self::createClient();
        $user = $this->createUser('franek@example.com', 'qwerty');
        $this->createAndLoginUser($client, 'franeknietensam@example.com', 'qwerty');

        $user->setPhoneNumber('201-203-245');
        $user->setUserName('cheesehead');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/user_apis/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'userName' => $user->getUserName(),
            'isMvp' => true
        ]);

        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);

        /**
         * @var $user UserApi
         */
        $user = $em->getRepository(UserApi::class)->find($user->getId());
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();

        $this->userLogin($client, 'franek@example.com', 'qwerty');

        $client->request('GET', '/api/user_apis/'.$user->getId());
        $this->assertJsonContains([
            'isMe' => true
        ]);

    }
}