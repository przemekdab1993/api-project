<?php


namespace App\Test;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\UserApi;
use Doctrine\ORM\EntityManagerInterface;

class  CustomApiTestCase extends ApiTestCase
{

    protected function createUser(string $email, string $password): UserApi
    {
        $user = new UserApi();
        $user->setEmail($email);
        $user->setUserName(substr($email, 0, strpos($email, '@')));

        $encoded = static::getContainer()->get('security.user_password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function createUserAdmin(string $email, string $password): UserApi
    {
        $user = new UserApi();
        $user->setEmail($email);
        $user->setUserName(substr($email, 0, strpos($email, '@')));
        $user->setRoles(['ROLE_ADMIN']);

        $encoded = static::getContainer()->get('security.user_password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function userLogin(Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'headers' => [ 'Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    protected function createAndLoginUser(Client $client, string $email, string $password): UserApi
    {
        $user = $this->createUser($email, $password);
        $this->userLogin($client, $email, $password);

        return $user;

        $this->assertResponseStatusCodeSame(204);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

}