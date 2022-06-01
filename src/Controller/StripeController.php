<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(Cart $cart, $reference,OrderRepository $order_repo,ProductRepository $prod_repo): RedirectResponse
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://localhost:8000';
        $order = $this->entityManager->getRepository(Order::class);
        $order = $order_repo->findOneByReference($reference);
        
       
        
       if (!$order) {

           return $this->redirectToroute('order');
       }
       
      

        \Stripe\Stripe::setApiKey('sk_test_51KzxsgGAlyplKjpCU45kiWukxM1qr6sjvIMBKt7xY1GOO2PkZWvq5ToAYirmF1W7xVboKe8tfuE2NX75DLRexhxb00tJpQZgMk');
            
        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $prod_repo->findOneByName($product->getProduct());
            $products_for_stripe[] = 
                [
                    'price_data' => [
                        'currency'=> 'eur',
                        'unit_amount'=> $product->getPrice(),
                        'product_data'=> [
                            'name'=> $product->getProduct(),
                            'images'=> [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                        ]
                    ],
                    'quantity'=> $product->getQuantity(),
                ];
        }
                // Prix livraison : 
        $products_for_stripe[] = [
            'price_data' => [
                'unit_amount' => $order->getCarrierPrice() * 100,
                'currency' => 'eur',
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],  
            'quantity' => 1,
        ];
           
        
            $checkout_session = \Stripe\Checkout\Session::create([
                'customer_email'=>$this->getUser()->getEmail(),
                'line_items' => [
                    $products_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);

        $order->setStripeCheckoutId($checkout_session->id);
        $this->entityManager->flush();

        header("HTTP/1.1 303 See Other");
        
       
       return $this->redirect($checkout_session->url);
        
             
    }
}
