<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\Entity\UserApi;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManager;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\DependencyInjection\Container;

class CheesesListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListing()
    {
        $client = self::createClient();

        $client->request('POST', 'api/cheeses', [
            'headers' => [ 'Content-Type' => 'application/json'],
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);



        $this->createAndLoginUser($client, 'ewelina@gmail.com', 'qwerty');

        $this->assertResponseStatusCodeSame(204);

        $client->request('POST', 'api/cheeses', [
            'headers' => [ 'Content-Type' => 'application/json'],
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(422);

//        $authenticatedUser = $this->createUserAndLogIn($client, 'ewelinakula@gmail.com', 'kula');
//        $otherUser = $this->createUser('juje@gmail.com','sra');
//
//        $cheesyData = [
//            "title" => "stringoser",
//            "price" => 2220,
//            "quantity" => 10,
//            "description" => "string"
//        ];
//
//        $client->request('POST', 'api/cheeses', [
//            'headers' => [ 'Content-Type' => 'application/json'],
//            'json' => $cheesyData
//        ]);
//        $this->assertResponseStatusCodeSame(201);
//
//
//
//        $client->request('POST', 'api/cheeses', [
//            'headers' => [ 'Content-Type' => 'application/json'],
//            'json' => $cheesyData + [ 'owner' => '/api/user_apis/'.$otherUser->getId()]
//        ]);
//        $this->assertResponseStatusCodeSame(422, 'not passing the correct owner');
//
//
//        $client->request('POST', 'api/cheeses', [
//            'headers' => [ 'Content-Type' => 'application/json'],
//            'json' => $cheesyData + [ 'owner' => '/api/user_apis/'.$authenticatedUser->getId()]
//        ]);
//        $this->assertResponseStatusCodeSame(201);

    }

//    public function testUpdateCheeseListing()
//    {
//        $client = self::createClient();
//        $user1 = $this->createUser('przemekd1@gmail.com', 'pomidor');
//        $user2 = $this->createUserAdmin('przemekd2@gmail.com', 'pomidor');
//
//        $cheeseListing = new CheeseListing('Dupka od sera');
//        $cheeseListing->setPrice(2200);
//        $cheeseListing->setQuantity(5);
//        $cheeseListing->setDescription('Nie wiem co tu wpisać.');
//        $cheeseListing->setOwner($user1);
//
//        $em = $this->getEntityManager();
//        $em->persist($cheeseListing);
//        $em->flush();
//
//        $this->logIn($client, 'przemekd1@gmail.com', 'pomidor');
//        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
//            'json' => ['quantity' => 100]
//        ]);
//        $this->assertResponseStatusCodeSame(200);
//
//
//        $this->logIn($client, 'przemekd2@gmail.com', 'pomidor');
//        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
//            'json' => [
//                'price' => 3333,
//                'owner' => '/api/user_apis/'.$user2->getId()
//            ]
//        ]);
//
//        $this->assertResponseStatusCodeSame(200);
//
//    }
//
//    public function testGetCheeseListingCollection()
//    {
//        $client = self::createClient();
//        $user = $this->createUser('duda@gmail.com', 'dupa');
//
//        $cheeseListing1 = new CheeseListing('cheese1');
//        $cheeseListing1->setOwner($user);
//        $cheeseListing1->setPrice(3313);
//        $cheeseListing1->setQuantity(3);
//        $cheeseListing1->setDescription('opis sera 1.');
//
//        $cheeseListing2 = new CheeseListing('cheese2');
//        $cheeseListing2->setOwner($user);
//        $cheeseListing2->setPrice(3013);
//        $cheeseListing2->setQuantity(5);
//        $cheeseListing2->setDescription('opis sera 2.');
//        $cheeseListing2->setIsPublished(true);
//
//        $cheeseListing3 = new CheeseListing('cheese3');
//        $cheeseListing3->setOwner($user);
//        $cheeseListing3->setPrice(1213);
//        $cheeseListing3->setQuantity(30);
//        $cheeseListing3->setDescription('opis sera 3.');
//        $cheeseListing3->setIsPublished(true);
//
//        $em = $this->getEntityManager();
//        $em->persist($cheeseListing1);
//        $em->persist($cheeseListing2);
//        $em->persist($cheeseListing3);
//
//        $em->flush();
//
//
//        $client->request('GET', '/api/cheeses');
//        $this->assertJsonContains(['hydra:totalItems' => 2]);
//    }
//
//    public function testGetCheeseListingItem()
//    {
//        $client = self::createClient();
//        $user = $this->createUser('duda@gmail.com', 'dupa');
//
//        $cheeseListing1 = new CheeseListing('cheese1');
//        $cheeseListing1->setOwner($user);
//        $cheeseListing1->setPrice(3313);
//        $cheeseListing1->setQuantity(3);
//        $cheeseListing1->setDescription('opis sera 1.');
//        $cheeseListing1->setIsPublished(false);
//
//
//        $em = $this->getEntityManager();
//        $em->persist($cheeseListing1);
//
//        $em->flush();
//
//
//        $client->request('GET', '/api/cheeses/'.$cheeseListing1->getId());
//        $this->assertResponseStatusCodeSame(404);
//    }
}