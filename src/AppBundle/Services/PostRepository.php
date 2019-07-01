<?php
namespace AppBundle\Services;
use Doctrine\ORM\EntityManager;
use BlogBundle\Entity\Post;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class PostRepository {

    private $em;
    private $container;
    private $repository;

    public function __construct(Container $container, EntityManager $em){
        $this->container= $container;
        $this->em = $em;
        $this->repository = $this->em->getRepository('BlogBundle:Post');
    }

    public function getAll():array{
         
        return $this->repository->findAll();
    }

    public function getById(int $id){

        return $this->repository->find($id);
    }

    public function createPost(Post $post):int{
        $this->em->persist($post);
        $this->em->flush();
        return $post->getId();
    }

    public function deletePost(int $id):string{
        $post = $this->getById($id);
        if (!$post){
            return ("No existe el post");
        }
        $this->em->remove($post);
        $this->em->flush();
        return ("Post Borrado");
    }

    public function updatePost(Post $post):bool{
        try {
         $this->em->flush();
             return true;
        }catch(Exception $e){
            return false;
        }
    }


}
