<?php

namespace App\Validator;

use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidIsPublishedValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\ValidIsPublished $constraint */
        if (!$value instanceof CheeseListing) {
            throw new \LogicException('Only CheeseListing is supported');
        }

        $originalData = $this->entityManager
            ->getUnitOfWork()
            ->getOriginalEntityData($value);

        $previousPublished = $originalData['isPublished'] ?? false;

        if ($previousPublished === $value->getIsPublished()) {
            //isPublished didn't change!
            return;
        }

        if ($value->getIsPublished()) {
            // we are publishing

            // don't allow short description, unless you are an admin
            if (strlen($value->getDescription()) < 20 && !$this->security->isGranted('ROLE_ADMIN')) {
                $this->context->buildViolation('Cannot publish: description is too short!')
                    ->atPath('description')
                    ->addViolation();
            }
            return;
        }

        //we are unPublished
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            //throw new AccessDeniedException('Only admin users can unpublished');

            $this->context->buildViolation('Normal user cannot unpublish')
                ->addViolation();
        }
    }
}
