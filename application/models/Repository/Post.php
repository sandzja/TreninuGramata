<?php
namespace Repository;

use Repository\AbstractRepository;

class Post extends AbstractRepository {
	
    public function getPublicPosts($limit = null, $offset = null, array $instances = array ()) {
    	$query = $this->createQueryBuilder('Post');
    	$query->where('Post.isPrivate = :isPrivate')->setParameter('isPrivate', false);
    	$query->orderBy('Post.id', 'DESC');
    	 
    	if ($instances != null) {
    		foreach ($instances as $instance) {
    			$queryInstances[] = 'Post INSTANCE OF ' . $instance;
    		}
    		$query->andWhere(implode(' OR ', $queryInstances));
    	}
    	 
    	if ($limit != null) {
    		$query->setMaxResults($limit);
    	}
    
    	if ($offset != null) {
    		$query->setFirstResult($offset);
    	}
    
    	return $query->getQuery()->getResult();
    }
    
    public function getOnlyUserPosts(\Entity\User $user, $limit = null, $offset = null, array $instances = array ()) {
    	$query = $this->createQueryBuilder('Post');
    	$query->where('Post.author = :author')->setParameter('author', $user);
    	$query->orderBy('Post.id', 'DESC');
    	
    	if ($instances != null) {
    		foreach ($instances as $instance) {
    			$queryInstances[] = 'Post INSTANCE OF ' . $instance;
    		}
    		$query->andWhere(implode(' OR ', $queryInstances));
    	}
    	
    	if ($limit != null) {
    		$query->setMaxResults($limit);
    	}
    
    	if ($offset != null) {
    		$query->setFirstResult($offset);
    	}
    
    	return $query->getQuery()->getResult();
    }
    
	public function getFriendsPosts(\Entity\User $user, $limit = null, $offset = null, array $instances = array ()) {
		$query = $this->createQueryBuilder('Post');
		$query->leftJoin('Post.author', 'FriendUser');
		$query->leftJoin('FriendUser.followers', 'CurrentUser');
		$query->leftJoin('Post.author', 'CurrentUser2');
		$query->where('CurrentUser = :user')->setParameter('user', $user);
		$query->andWhere('Post.isPrivate = :isPrivate')->setParameter('isPrivate', false);
		$query->orWhere('CurrentUser2 = :user2')->setParameter('user2', $user);
		$query->groupBy('Post.id');
		$query->orderBy('Post.id', 'DESC');
		
		if ($instances != null) {
	 		foreach ($instances as $instance) {
	 			$queryInstances[] = 'Post INSTANCE OF ' . $instance;
	 		}
	 		$query->andWhere(implode(' OR ', $queryInstances));
		}

		if ($limit != null) {
			$query->setMaxResults($limit);
		}
		
		if ($offset != null) {
		    $query->setFirstResult($offset);
		}

		return $query->getQuery()->getResult();
	}
	
}