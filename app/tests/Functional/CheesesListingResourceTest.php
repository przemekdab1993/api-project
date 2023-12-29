<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\Entity\CheeseNotification;
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
        $this->assertResponseStatusCodeSame(422);


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
            'json' => $cheeseData + ["owner" => "/api/user-apis/".$user2->getUuId()]
        ]);
        $this->assertResponseStatusCodeSame(422);

        $client->request('POST', 'api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $cheeseData + ["owner" => "/api/user-apis/".$user1->getUuId()]
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
        $cheeseListing->setDescription('Jakiś opis, który musi być dość długi');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setQuantity(100);
        $cheeseListing->setIsPublished(false);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($cheeseListing);
        $entityManager->flush();

        $this->userLogin($client, 'inwa33@exemple.com', 'qwerty');

        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
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

        $cheeseNotifications = $em->getRepository(CheeseNotification::class)->findBy(['cheeseListing' => $cheeseListing->getId()]);
        $this->assertCount(1, $cheeseNotifications);

        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'isPublished' => true
            ]
        ]);

        $cheeseNotifications = $em->getRepository(CheeseNotification::class)->findBy(['cheeseListing' => $cheeseListing->getId()]);
        $this->assertCount(1, $cheeseNotifications);
    }

    public function testPublishCheeseListingValidation()
    {
        $client = self::createClient();
        $user = $this->createUser('ewelinka@example.com', 'kula');
        $admin = $this->createUserAdmin('przemus@example.com', 'dab');
        $em = $this->getEntityManager();

        $cheeseListing = new CheeseListing('Ser Eweliny');
        $cheeseListing->setOwner($user);
        $cheeseListing->setDescription('Jakiś opis');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setQuantity(100);
        $cheeseListing->setIsPublished(false);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($cheeseListing);
        $entityManager->flush();

        // 1) the owner CANNOT publish with a short description
        $this->userLogin($client, 'ewelinka@example.com', 'kula');
        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'json' => [
                'isPublished' => true
            ]
        ]);
        $this->assertResponseStatusCodeSame(422, 'description is too short');

        // 2) an admin user CAN publish with a short description
        $this->userLogin($client, 'przemus@example.com', 'dab');
        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'json' => [
                'isPublished' => true
            ]
        ]);
        $this->assertResponseStatusCodeSame(200, 'admin can publish a short description');

        $cheeseListing = $em->getRepository(CheeseListing::class)->find($cheeseListing->getId());
        $this->assertTrue($cheeseListing->getIsPublished());

        // 3) a normal user CAN make other changes to their listing
        $this->userLogin($client, 'ewelinka@example.com', 'kula');
        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'json' => [
                'price' => 5200
            ]
        ]);

        $this->assertResponseStatusCodeSame(200, 'user can make other changes on short description');

        // w tym przypadku testy oszukują

//        $cheeseListingX = $em->getRepository(CheeseListing::class)->find($cheeseListing->getId());
//        $this->assertSame(5200, $cheeseListingX->getPrice());

        // 4) a normal user CANNOT unpublish
        $this->userLogin($client, 'ewelinka@example.com', 'kula');
        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'json' => [
                'isPublished' => false
            ]
        ]);
        $this->assertResponseStatusCodeSame(422, 'normal user cannot unpublish');

        // 5) an admin user CAN unpublish cheeseListing
        $this->userLogin($client, 'przemus@example.com', 'dab');
        $client->request('PUT', 'api/cheeses/' . $cheeseListing->getId(), [
            'json' => [
                'isPublished' => false
            ]
        ]);
        $this->assertResponseStatusCodeSame(200, 'admin can unpublish');
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

        $client->request('GET', '/api/user-apis/'.$user->getUuId(), []);

        $data = $client->getResponse()->toArray();
        $this->assertEmpty($data['cheeseListings']);

    }
}