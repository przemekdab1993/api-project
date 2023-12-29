<?php

namespace App\Tests\Functional;

use App\Entity\UserApi;
use App\Repository\UserApiRepository;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Ramsey\Uuid\Uuid;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function  testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/user-apis', [
            'json' => [
                'email' => 'kreda@example.com',
                'userName' => 'kreda',
                'password' => 'kreda'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $em = $this->getEntityManager();
        $user = $em->getRepository(UserApi::class)->findOneBy(['email' => 'kreda@example.com']);
        $this->assertNotNull($user);
        $this->assertJsonContains([
            '@id' => '/api/user-apis/'.$user->getUuid()->toString()
        ]);

        $this->userLogin($client, 'kreda@example.com', 'kreda');
    }

    public function  testCreateUserWithUuid()
    {
        $client = self::createClient();
        $uuid = Uuid::uuid4();

        $client->request('POST', '/api/user-apis', [
            //'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'uuid' => $uuid,
                'email' => 'ekwador@example.com',
                'userName' => 'ekwador',
                'password' => 'ekwador'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertJsonContains([
            '@id' => '/api/user-apis/'.$uuid
        ]);
    }

    public function testUpdateUser()
    {
        $client = self::createClient();
        $user = $this->createAndLoginUser($client, 'duda@example.com', 'Andrzej');

        $client->request('PUT', '/api/user-apis/'.$user->getUuId(), [
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

        $client->request('GET', '/api/user-apis/'.$user->getUuId());
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

        $client->request('GET', '/api/user-apis/'.$user->getUuId());
        $this->assertJsonContains([
            'isMe' => true
        ]);

    }
}