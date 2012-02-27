<?php
namespace Repository;

use Repository\AbstractRepository;

class Message extends AbstractRepository {
	
	public function getMessages(\Entity\User $user, \DateTime $timestamp) {
		$query = $this->createQueryBuilder('Message');
		$query->where('Message.receiverUser = :receiverUser')->setParameter('receiverUser', $user);
		$query->andWhere('Message.dateSent >= :timestamp ')->setParameter('timestamp', $timestamp);
		
		return $query->getQuery()->getResult();
	}
}