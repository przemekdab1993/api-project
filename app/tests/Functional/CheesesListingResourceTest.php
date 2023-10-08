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
            'headers' => ['Content-Type' => 'application/json'],
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);


        $user1 = $this->createAndLoginUser($client, 'ewelina@gmail.com', 'qwerty');
        $user2 = $this->createUser('ewelina2@gmail.com', 'qwerty');

        $cheeseData = [
            "title" => "cheese suso uno pulto",
            "price" => 2100,
            "quantity" => 10,
            "description" => "string"
        ];

        $client->request('POST', 'api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheeseData
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', 'api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheeseData + ["owner" => "/api/user_apis/".$user2->getId()]
        ]);
        $this->assertResponseStatusCodeSame(422);

        $client->request('POST', 'api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheeseData + ["owner" => "/api/user_apis/".$user1->getId()]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();

        $user1 = $this->createUser('inwa1@exemple.com', 'qwerty');
        $user2 = $this->createUser('inwa2@exemple.com', 'qwerty');

        $cheeseListing = new CheeseListing('Boo cheese');
        $cheeseListing->setOwner($user1);
        $cheeseListing->setDescription('Jakiś opis');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setQuantity(100);
        $cheeseListing->setIsPublished(false);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($cheeseListing);
        $entityManager->flush();

        $this->userLogin($client, 'inwa2@exemple.com', 'qwerty');

        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Janusz'
            ]
        ]);
        $this->assertResponseStatusCodeSame(404);

        $this->userLogin($client, 'inwa1@exemple.com', 'qwerty');

        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Duda cheese',
                'description' => 'Duda cheese'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPublishCheeseListing()
    {
        $client = self::createClient();

        $user = $this->createUser('inwa33@exemple.com', 'qwerty');

        $cheeseListing = new CheeseListing('Boo cheese');
        $cheeseListing->setOwner($user);
        $cheeseListing->setDescription('Jakiś opis');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setQuantity(100);
        $cheeseListing->setIsPublished(false);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($cheeseListing);
        $entityManager->flush();

        $this->userLogin($client, 'inwa33@exemple.com', 'qwerty');

        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'isPublished' => true
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);

        $em = $this->getEntityManager();

        /**
         * @var $cheeseListing CheeseListing
         */
        $cheeseListing = $em->getRepository(CheeseListing::class)->find($cheeseListing->getId());

        $this->assertTrue($cheeseListing->getIsPublished());
    }

    public function testGetCheesesListingCollection()
    {
        $client = self::createClient();
        $user = $this->createUser('przemek@example.com', 'qwerty123');

        $cheeseListing1 = new CheeseListing('Boo cheese 1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setDescription('Jakiś opis');
        $cheeseListing1->setPrice(1000);
        $cheeseListing1->setQuantity(100);
        $cheeseListing1->setIsPublished(false);

        $cheeseListing2 = new CheeseListing('Boo cheese 2');
        $cheeseListing2->setOwner($user);
        $cheeseListing2->setDescription('Jakiś opis');
        $cheeseListing2->setPrice(1000);
        $cheeseListing2->setQuantity(100);
        $cheeseListing2->setIsPublished(true);

        $cheeseListing3 = new CheeseListing('Boo cheese 3');
        $cheeseListing3->setOwner($user);
        $cheeseListing3->setDescription('Jakiś opis');
        $cheeseListing3->setPrice(1000);
        $cheeseListing3->setQuantity(100);
        $cheeseListing3->setIsPublished(true);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->persist($cheeseListing2);
        $em->persist($cheeseListing3);
        $em->flush();

        $client->request('GET', '/api/cheeses', []);
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheesesListingItem()
    {
        $client = self::createClient();
        $user = $this->createAndLoginUser($client, 'przemek2@example.com', 'qwerty123');

        $cheeseListing = new CheeseListing('Boo cheese');
        $cheeseListing->setOwner($user);
        $cheeseListing->setDescription('Jakiś opis');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setQuantity(100);
        $cheeseListing->setIsPublished(false);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $client->request('GET', '/api/cheeses/'.$cheeseListing->getId(), []);
        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/user_apis/'.$user->getId(), []);

        $data = $client->getResponse()->toArray();
        $this->assertEmpty($data['cheeseListings']);

    }
}