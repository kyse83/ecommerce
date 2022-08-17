<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'contact')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()) 
        {
            $this->addFlash('notice','Mails a gérer pas encore fait');
            $mail = new Mail();
            #$mail->send('martial.4@hotmail.fr',"La boutique Française","vous avez reçu un mail");
            #mail à traiter... $form->getData()
        }

        return $this->render('contact/index.html.twig',[
            'form'=>$form->createView()
        ]
    );
    }
}
