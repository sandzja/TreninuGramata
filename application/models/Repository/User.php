<?php
namespace Repository;

use Doctrine\ORM\Query\ResultSetMapping;

use Repository\AbstractRepository;

class User extends AbstractRepository {
	
	public function searchFollowing(\Entity\User $user, $name, $limit = null, $offset = null) {
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult('\Entity\User', 'User');
		$rsm->addFieldResult('User', 'id', 'id');
		$rsm->addFieldResult('User', 'name', 'name');
		$rsm->addFieldResult('User', 'profileImageUrl', 'profileImageUrl');
		
		$sql = 'SELECT u1_.* FROM User u0_ INNER JOIN Friend f2_ ON u0_.id = f2_.user_id INNER JOIN User u1_ ON u1_.id = f2_.friend_user_id WHERE u0_.id = ? AND u1_.name LIKE ?';
		
		if ($limit != null) {
		    $sql .= ' LIMIT ' . $limit;
		}
		
		if ($offset != null) {
		    $sql .= ' OFFSET ' . $offset;
		}
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameter(1, $user->getId());
		$query->setParameter(2, '%' . $name . '%');
	
		return $query->getResult();
	}
	
	public function searchFollowers(\Entity\User $user, $name, $limit = null, $offset = null) {
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult('\Entity\User', 'User');
		$rsm->addFieldResult('User', 'id', 'id');
		$rsm->addFieldResult('User', 'name', 'name');
		$rsm->addFieldResult('User', 'profileImageUrl', 'profileImageUrl');
		
		$sql = 'SELECT u1_.* FROM User u0_ INNER JOIN Friend f2_ ON u0_.id = f2_.friend_user_id INNER JOIN User u1_ ON u1_.id = f2_.user_id WHERE u0_.id = ? AND u1_.name LIKE ?';
		
		if ($limit != null) {
			$sql .= ' LIMIT ' . $limit;
		}
		
		if ($offset != null) {
			$sql .= ' OFFSET ' . $offset;
		}
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameter(1, $user->getId());
		$query->setParameter(2, '%' . $name . '%');
	
		return $query->getResult();
	}
	
	public function searchUsers($name, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('User');
		$query->where('User.name LIKE :name')->setParameter('name', '%' . $name . '%');
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
	
		return $query->getQuery()->getResult();
	}
}