<?php
 
namespace App\Classe;
 
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
 
 
class Cart
{
    private $stack;
    private $entityManager;
 
    public function __construct(EntityManagerInterface $entityManager,RequestStack $stack)
 
    {
         $this->stack = $stack;
         $this->entityManager = $entityManager;
    }
 
    public function add($id)
    {

        $session = $this->stack->getSession();
        $cart = $session->get('cart',[]);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        }else {
            $cart[$id] = 1;
        }
         $session->set('cart',$cart);
    }
    public function get()
    {
        return $this->stack->getSession()->get('cart');
    }
    public function remove()
    {
        return $this->stack->getSession()->remove('cart');
    }
    public function delete($id)
    {
        $session = $this->stack->getSession();
        $cart = $session->get('cart',[]);
        unset($cart[$id]);

        return $this->stack->getSession()->set('cart',$cart);
    }
    public function decrease($id)
    {
        $session = $this->stack->getSession();
        $cart = $session->get('cart',[]);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        }else{
            unset($cart[$id]);
        }
        return $this->stack->getSession()->set('cart',$cart);
    }

    public function getFull()
    {
        
        $cartComplete = [];

        if ($this->get()) {
            foreach ($this->get() as $id =>$quantity){
                $product_object = $this->entityManager->getRepository(Product::class)->findOneBy(['id'=>$id]);
                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product'=>$product_object,
                    'quantity'=> $quantity
                ];
            }
        }
        return $cartComplete;
    }



 
}