<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Post;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * @Route("/post")
 */
class PostController extends Controller
{

    public function serializePost( Post $post)
    {
        return array(
            'title'=>$post->getTitle(),
            'body'=>$post->getBody(),
            'user'=>$post->getUser(),
            'tag'=>$post->getTag(),
            'createAt'=>$post->getCreateAt(),
            'updateAt'=>$post->getUpdateAt()
        );
    }

    public function result($clave, $valor=null){

        return array($clave=>$valor);

    }

    /**
     * @Method({"GET"})
     * @Route("/")
    */
    public function getAllPostsAction(){

        $encoders = [new JsonEncoder()];
        //$normalizers = [new ObjectNormalizer()];
        $normalizer = array(new DateTimeNormalizer(), new ObjectNormalizer());
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setIgnoredAttributes(array('createAt','user'));
        $normalizers= array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        
        
        $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Post::class);
            $posts = $repository->findAll();
            //$data= array('post'=>array());
           // foreach( $posts as $post){
            //    $data['post'][]= $this->serializePost($post);
           // }
            
           $jsonContent = $serializer->serialize($posts, 'json');
           // var_dump($jsonContent);
            //$response = new JsonResponse($data,200);
            //Faltaría refactorizar.
            $response = new Response($jsonContent);
           return $response;
    }

    /**
     * @Method({"GET"})
     * @Route ("/{id}")
     */
    public function getPostByIdAction($id)
    {

        $em= $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Post::class);
        $post = $repository->find($id);
        if(!$post) return new JsonResponse($this->result('resultado','El objeto no existe->'.$id),404);
        $data = $this->serializePost($post);
        $response = new JsonResponse($this->result('Post',$data), 200);
        return $response;
        
    }

    /**
     * @Method({"DELETE"})
     * @Route("/{id}")
     */
    public function deletePostByIdAction($id)
    {
        try{
            $em= $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Post::class);
            $post = $repository->find($id);
            if (!$post){
                return new JsonResponse($this->result('resultado','El objeto no existe->'.$id),404);
            }
            $em->remove($post);
            $em->flush();
            return new JsonResponse($this->result('Se ha eliminado',$id),200);
        }catch(Exception $e){
            return new JsonResponse($this->result('Error',$e->message()),500);
        }
          
    }

    /**
     * @Method({"POST"})
     * @Route("/")
     */
    public function createPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $parameters = [];
        if($content = $request->getContent()){
              $parameters = json_decode($content, true);
                $post = new Post();
                $post->setTitle($parameters['title']);
                $post->setBody($parameters['body']);
                $post->setTag($parameters['tag']);
                $post->setCreateAt(new \DateTime('now'));     
               
                $em->persist($post);
                $em->flush();

        }
        return new JsonResponse($this->result("Se ha creado el Post- >",$post->getId()));

    }

    /**
     * @Route("/{id}", methods={"PUT","PATCH"})
     */
    public function updatePostAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository("BlogBundle:Post")->find($id);
        if (!$post){
            return new JsonResponse($this->result("El post no existe->",$id));
        }
        if($content = $request->getContent()){
            $parameters = json_decode($content, true);
            if(isset($parameters['title'])) $post->setTitle($parameters['title']);
            if(isset($parameters['body'])) $post->setBody($parameters['body']);
            if(isset($parameters['tag'])) $post->setTag($parameters['tag']);
              $post->setUpdateAt(new \DateTime('now'));     
              $em->persist($post);
              $em->flush();

      }
       return new JsonResponse($this->result("post", $post));

    }

}
