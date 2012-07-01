<?php

namespace Mondel\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function readNotifications($userId, $postId)
    {
		$repository  = $this->_em->getRepository('MondelUserBundle:UserPostSuscription');
		$suscription = $repository->findOneBy(array(
			'post' => $postId,
			'user' => $userId
		));

		if (!is_null($suscription)) {
		    foreach ($suscription->getNotifications() as $notification) {
		    	$notification->setIsRead(true);
		    }
		    $this->_em->flush();
		}
    }
}