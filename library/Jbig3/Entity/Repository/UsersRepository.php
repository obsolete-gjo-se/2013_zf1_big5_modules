<?php
namespace Jbig3\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UsersRepository extends EntityRepository {

    public function findAllUsers(){
        
        $dql = 'SELECT u FROM Jbig3\Entity\UsersEntity u';
        
        $users = $this->_em->createQuery($dql)->getResult();
        return $users;
    }

    public function getRoleIdArray($userId){
        
        $roles = array();
        
        $dql = 'SELECT PARTIAL r.{id} 
                FROM Jbig3\Entity\RolesEntity r
                JOIN r.users u
                WHERE u.id = ?1';
        
        $roleEntities = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter(1, $userId)
            ->getResult();
        
        foreach ($roleEntities as $role) {
            $roless[] = $role->id;
        }
        
        return $roles;
    }

    public function getRoleNameArray($userId){
        
        $roles = array();
        
        $dql = 'SELECT rl FROM Jbig3\Entity\RolesEntity rl
                    JOIN rl.users u
                    WHERE u.id = ?1';
        
        $roleEntities = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter(1, $userId)
            ->getResult();
        
        foreach ($roleEntities as $role) {
            $roles[] = $role->name;
        }
        
        return $roles;
    }

}