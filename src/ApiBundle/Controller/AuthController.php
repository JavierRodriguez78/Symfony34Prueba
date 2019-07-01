<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use BlogBundle\Entity\User;

/**
 * @Route("/auth")
 */
class AuthController extends Controller
{

    public function result($clave, $valor=null){

        return array($clave=>$valor);

    }

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(Request $request)
    {
        $parameters = [];
        if($content = $request->getContent()){
            $parameters = json_decode($content, true);
            $username = $parameters['username'];
            $password=  $parameters['password'];
            $name= $parameters['name'];
            $mail= $parameters['mail'];
            $user = new User($username);
            $user->setPassword($password);
            $user->setName($name);
            $user->setMail($mail);
            $userRepositoryService = $this->get("app.user_repository");
            $id = $userRepositoryService->createUser($user);
            return new JsonResponse($this->result("Se ha creado el usuario", $id));
        }
    }

}
