<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\UserApi;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private DataPersisterInterface $decoratedDataPersister,
        private UserPasswordHasherInterface $userPasswordHasher,
        private LoggerInterface $logger
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof UserApi;
    }

    /**
     * @param UserApi $data
     */
    public function persist($data, array $context = [])
    {
        if (($context['item_operation_name'] ?? null) === 'put') {
            $this->logger->info(sprintf('User %s begin updated', $data->getId()));
        }

        if (!$data->getId()) {
            $this->logger->info(sprintf('User %s registered! Eureka', $data->getEmail()));
        }

        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
        }

        $data->eraseCredentials();
        $this->decoratedDataPersister->persist($data);
    }

    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove();
    }
}