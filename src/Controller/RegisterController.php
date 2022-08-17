<?php

namespace App\Controller;

use App\Entity\User;
use App\Classe\Mail;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{


    public function __construct(private ManagerRegistry $doctrine,private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/inscription', name: 'register')]
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()) {
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail(($user->getEmail()));
          
            if (!$search_email)
            {
                $password = $user->getPassword();
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);
       
                $em = $this->doctrine->getManager();
                $em->persist($user);
                $em->flush();
                $mail = new Mail();
                
                $content = "Bonjour ".$user->getfirstname()."<br/>Bienvenue sur la boutique made in France.</br>Tu vas pouvoir acheter pleins de trucs inutiles.";
                $mail->send($user->getEmail(),$user->getfirstname(),'Bienvenue sur la boutique Française',$content);
                $notification = "Votre inscription s'est correctement déroulée, vous pouvez dés à présent vous connecter à votre compte.";
       
            }else{
                
                $notification = "L'email que vous avez renseignée existe déjà";
            }


        }
        return $this->render('register/index.html.twig',[
            'form'=>$form->createView(),
            'notification'=> $notification
        ]);
    }
}
