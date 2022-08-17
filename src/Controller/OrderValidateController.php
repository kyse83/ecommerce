<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeCheckoutId}', name: 'order_validate')]
    public function index(Cart $cart,$stripeCheckoutId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeCheckoutId($stripeCheckoutId);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        if ($order->getState()==0) {
            $cart->remove();
            $order->setState(1);
            $this->entityManager->flush();

            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getfirstname()."<br/>Merci pour votre commande.</br>Tu as acheté pleins de trucs inutiles.";
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getfirstname(),'Votre commande sur la boutique Française est validée',$content);
           

        }
        
       
        return $this->render('order_validate/index.html.twig',[
            'order'=>$order
        ]);
    }
}
