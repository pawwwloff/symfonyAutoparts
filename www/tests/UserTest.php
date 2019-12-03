<?php

namespace App\Document;


class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @Route("/mongoTest")
     * @Method("GET")
     */
    public function mongoTest()
    {
        $user = new User();
        $user->setEmail("hello@medium.com");
        $user->setFirstname("Matt");
        $user->setLastname("Matt");
        $user->setPassword(md5("123456"));
        $dm = $this->get('doctrine_mongodb')->getManager();

        $dm->persist($user);
        $dm->flush();
        return new JsonResponse(array('Status' => 'OK'));
    }
}
