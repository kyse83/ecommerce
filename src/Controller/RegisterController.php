<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{


    public function __construct(private ManagerRegistry $doctrine)
    {
        
    }
    #[Route('/inscription', name: 'register')]
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()) {
            $user = $form->getData();
            $password = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);
   
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

        }
        return $this->render('register/index.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
