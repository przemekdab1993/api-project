<?php


namespace App\Doctrine;

use App\Entity\UserApi;

class UserSetIsMvpListener
{

    public function postLoad(UserApi $user)
    {
        $user->setIsMvp(strpos($user->getUserName(), 'cheese') !== false);
    }
}