<?php
namespace AppBundle\Services;
use Doctrine\ORM\EntityManager;
use BlogBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class UserRepository {

    private $em;
    private $container;
    private $repository;

    public function __construct(Container $container, EntityManager $em){
        $this->container= $container;
        $this->em = $em;
        $this->repository = $this->em->getRepository('BlogBundle:User');
    }

    public function getAll():array{
         
        return $this->repository->findAll();
    }

    public function getById(int $id){

        return $this->repository->find($id);
    }

    public function createUser(User $user):int{
        $this->em->persist($user);
        $this->em->flush();
        return $user->getId();
    }

    public function deleteUser(int $id):string{
        $user = $this->getById($id);
        if (!$user){
            return ("No existe el user");
        }
        $this->em->remove($user);
        $this->em->flush();
        return ("User Borrado");
    }

    public function updatePost(User $user):bool{
        try {
         $this->em->flush();
             return true;
        }catch(Exception $e){
            return false;
        }
    }


}
