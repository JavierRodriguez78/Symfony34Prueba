<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use BlogBundle\Entity\Post;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
            'createAt'=>$post->getCreateAt()
        );
    }

    /**
     * @Method({"GET"})
     * @Route("/")
    */
    public function getAllPostsAction(){

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        
        
        $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Post::class);
            $posts = $repository->findAll();
            $data= array('post'=>array());
            foreach( $posts as $post){
                $data['post'][]= $this->serializePost($post);
            }
            
           // $jsonContent = $serializer->serialize($posts, 'json');
           // var_dump($jsonContent);
            $response = new JsonResponse($data,200);
           return $response;
    }

}
