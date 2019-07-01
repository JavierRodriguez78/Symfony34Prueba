<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use BlogBundle\Entity\Post;

/**
 * @Route("/post")
 */
class PostController extends Controller

{
    private $em;

    public function __construct()
    {
       
    }

    /**
     * @Route("/add")
     */
    public function addAction(){
       
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BlogBundle:User');

        //Creamos usuario
        $user = $repository->find(1);




        //Creamos la entidad
        $post = new Post();
        $post->setTitle('Prueba con user3');
        $post->setBody('Es el cuerpo');
        $post->setTag('untag');
        $post->setCreateAt(new \DateTime('now'));
        $post->setUser($user);
        
        //Persistimos la entidad
      
        //$em->persist($post);
       // $em->flush();
       $postRepositoryService = $this->get("app.post_repository");
       $id= $postRepositoryService->createPost($post);

        return new Response("Post creado ->".$id);

    }


    /**
     * @Route("/getAll")
     */
    public function getAllAction(){
        $postRepositoryService = $this->get("app.post_repository");

        //Recuperar el Manager
        //$em = $this->getDoctrine()->getManager();
        //$repository = $em->getRepository('BlogBundle:Post');
        //$posts = $repository->findAll();
        $posts = $postRepositoryService->getAll();        
        return $this->render('@Blog/Default/posts.html.twig',['posts'=>$posts]);
    }
    /**
     * @Route("/getallfilter")
     */
    public function getAllFilterAction(){

        //Recuperar el Manager
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p FROM BlogBundle:Post p'
        );
        $posts = $query->getResult();
        return $this->render('@Blog/Default/posts.html.twig',['posts'=>$posts]);
    }

    /**
     * @Route("/find/{id}")
     */
    public function getPostById($id)
    {
        //$em = $this->getDoctrine()->getManager();
        //$repository= $em->getRepository("BlogBundle:Post");
        $postRepositoryService = $this->get("app.post_repository");
        $post = $postRepositoryService->getById($id);
        if ($post) return $this->render('@Blog/Default/post.html.twig',['post'=>$post]);
        return new Response("No existe ningún post con el id ->".$id);    
    }
    /**
     * @Route("/findtitle/{title}")
     */
    public function getPostByTitle($title)
    {
        $em = $this->getDoctrine()->getManager();
        $repository= $em->getRepository("BlogBundle:Post");
        $post = $repository->findBy(array('title'=>"Prueba",
                                          'tag'=>'tag'));
        if (!$post){
            return new Response("No existe el post");
        }
        return $this->render('@Blog/Default/posts.html.twig',['posts'=>$post]);
    }
     /**
     * @Route("/findquery/{title}")
     */
    public function getPostByQuery($title)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BlogBundle:Post');
        $query = $repository->createQueryBuilder('p')
        ->where('p.title LIKE :title')
        ->setParameter('title',$title)
        ->getQuery();
        $post = $query->getResult();
        return $this->render('@Blog/Default/posts.html.twig',['posts'=>$post]);
    }
     /**
     * @Route("/deletePost/{id}")
     */
    public function deletePost($id)
    {
        // $em = $this->getDoctrine()->getManager();
        // $repository = $em->getRepository('BlogBundle:Post');
        // $post = $repository->find($id);
        // if (!$post){
        //     return new Response("No existe el post");
        // }
        // $em->remove($post);
        // $em->flush();
        $postRepositoryService = $this->get("app.post_repository");
        $post = $postRepositoryService->deletePost($id);
        return new Response($post);

    }
     /**
     * @Route("/updatePost/{id}")
     */
    public function updatePost($id)
    {
        $postRepositoryService = $this->get("app.post_repository");
        $post= $postRepositoryService->getById($id);    
        if (!$post){
            return new Response("No existe el post");
        }
        $post->setTitle("OtroPost");
       return ($postRepositoryService->updatePost($post) == true)
       ? $this->redirect('/blog/post/getAll')
       : new Response("Error en la actualización");
    }


}
