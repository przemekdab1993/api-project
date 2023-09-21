<?php

namespace App\Serializer\Normalizer;

use App\Entity\UserApi;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;


class UserApiNormalizer //implements ContextAwareNormalizerInterface , CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_NORMALIZER_ALREADY_CALLED';

    /**
     * @param UserApi $object
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $isOwner = $this->userIsOwner($object);

        if ($isOwner) {
            $context['groups'][] = 'owner:read';
        }

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        // Here: add, edit, or delete some data
        $data['isMe'] = $isOwner;

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof UserApi;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }

    private function userIsOwner(UserApi $user):bool
    {
        /**
         * @var UserApi|null $userAuthenticatedUser
         */
        $userAuthenticatedUser = $this->security->getUser();

        if ($userAuthenticatedUser) {
            return $userAuthenticatedUser->getUserIdentifier() === $user->getUserIdentifier();
        }

        return false;
    }
}
