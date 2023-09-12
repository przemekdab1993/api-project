<?php

namespace App\Tests\Functional;

use App\Entity\UserApi;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

//    public function testCreateUser()
//    {
//        $client = self::createClient();
//
//        $client->request('POST', '/api/user_apis', [
//            'json' => [
//                'email' => 'user@example.com',
//                'password' => 'string',
//                'userName' => 'userXD',
//            ]
//        ]);
//
//        $this->assertResponseStatusCodeSame(201);
//
//        $this->logIn($client, 'user@example.com', 'string');
//
//    }
//
//    public function testUpdateUser()
//    {
//        $client = self::createClient();
//
//        $user= $this->createUserAndLogIn($client, 'pola@gmail.com', 'duda');
//
//        $client->request('PUT', '/api/user_apis/'.$user->getId(), [
//            'json' => [
//                'userName' => 'userXD',
//                'roles' => ['ROLE_ADMIN'], // will be ignored
//            ]
//        ]);
//
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains([
//            'userName' => 'userXD'
//        ]);
//
//        $em = $this->getEntityManager();
//
//        /**
//         * @var UserApi $user
//         */
//        $user = $em->getRepository(UserApi::class)->find($user->getId());
//
//        $this->assertEquals(['ROLE_USER'], $user->getRoles());
//
//    }
//
//    public function testGetUser()
//    {
//        $client = self::createClient();
//        $user = $this->createUser( 'ada@gmail.com', 'jajko');
//
//        $this->createUserAndLogIn($client, 'ada2@gmail.com', 'jajko2');
//
//        $user->setPhoneNumber('555-321-432');
//        $em = $this->getEntityManager();
//        $em->flush();
//
//        $client->request('GET', '/api/user_apis/'.$user->getId());
//
//        $this->assertJsonContains([
//            'userName' => 'ada'
//        ]);
//
//        $data = $client->getResponse()->toArray();
//        $this->assertArrayNotHasKey('phoneNumber', $data);
//
//        // refresh the user &elevate
//        $user = $em->getRepository(UserApi::class)->find($user->getId());
//        $user->setRoles(['ROLE_ADMIN']);
//        $em->flush();
//
//        $this->logIn($client, 'ada@gmail.com', 'jajko');
//
//        $client->request('GET', '/api/user_apis/'.$user->getId());
//
//        $this->assertJsonContains([
//            'phoneNumber' => '555-321-432'
//        ]);
//    }

}