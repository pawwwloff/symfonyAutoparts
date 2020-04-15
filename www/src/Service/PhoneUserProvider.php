<?php

namespace App\Service;

use FOS\UserBundle\Security\UserProvider;

class PhoneUserProvider extends UserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser($phoneOrEmail)
    {
        /** TODO сделать проверки для телефона например если будет  (79232276218 добавить  + и тд) */
        if (preg_match('/^.+\@\S+\.\S+$/', $phoneOrEmail)) {
            $user = $this->userManager->findUserByEmail($phoneOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->userManager->findUserBy(array('phone' => $phoneOrEmail));
    }
}