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
        $this->assertResponseStatusCodeSame(403);

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
}