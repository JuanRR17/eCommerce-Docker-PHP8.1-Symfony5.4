<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\BasketRow;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class BasketController extends AbstractController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em,
    )
    {
        $this->em = $em;
    }

    public function index(?UserInterface $user): Response
    {
    if($user){     
        $basket = $this->em->getRepository('App:Basket')->findOneBy([
            'userid' => $user]); 
            if($basket){
            $total = $basket->getTotal();
        }
    }elseif($basketNoUser = $this->em->getRepository('App:Basket')->findOneBy([
        'userid'=>null])){
            $basket = $basketNoUser;
            $total = $basket->getTotal();
        }
        return $this->render('basket/index.html.twig', [
            'basket' => $basket,
            'total' => $total
        ]);
    }

    public function add(Product $product){
        // $basket = $this->em->getRepository('App:Basket')->findAll();
        $basket = $this->em->getRepository('App:Basket')->findOneBy(['userid' => $this->getUser()]);

        if($basket==null){
            $basket = new Basket();
            $basket->setTotal(0);
            $basket->setUserid($this->getUser());
            $this->em->persist($basket);
            $this->em->flush();
        }
        // dump($basket);
        // die();

        if(isset($product)){
            $new_row=$this->em->getRepository('App:BasketRow')->findOneBy(['product_id' => $product]);
            if(!isset($new_row)){
                $basket_row = new BasketRow();
                $basket_row->setQuantity(1)
                            ->setBasketId($basket)
                            ->setProductId($product)
                            ->setSubtotal();
                $this->em->persist($basket_row);
                $this->em->flush();
                $basket->setTotal($basket->getTotal()+$product->getPrice());
                $this->em->persist($basket);
                $this->em->flush();
            }else{
                $basket_row = $new_row;
                $basket_row->setQuantity($basket_row->getQuantity()+1)->setSubtotal();
                $this->em->persist($basket_row);
                $this->em->flush();
                $basket->setTotal($basket->getTotal()+$product->getPrice());
                $this->em->persist($basket);
                $this->em->flush();
            }
            return $this->redirect($this->generateUrl('basket'));
        }

        return $this->render('basket/index.html.twig',[
            'basket' => $basket
        ]);
    }

    public function emptyBasket(){
        $basket = $this->em->getRepository('App:Basket')->findAll()[0];
        $this->em->remove($basket);
        $this->em->flush();

        return $this->redirect($this->generateUrl('basket'));
    }
    
    public function removefrombasket(BasketRow $basket_row){
        if(isset($basket_row)){
            $basket = $basket_row->getBasketId();
            $basket->setTotal($basket->getTotal() - $basket_row->getSubtotal() );
            $this->em->remove($basket_row);
            $this->em->flush();
            $basket_rows = $this->em->getRepository('App:BasketRow')->findAll();
            if(empty($basket_rows)){
                $this->em->remove($basket);
                $this->em->flush(); 
            }
        }
        return $this->redirect($this->generateUrl('basket'));
    }

    public function up(BasketRow $basket_row){
        
        if(isset($basket_row)){
            $this->add($basket_row->getProductId());
        }

        return $this->redirect($this->generateUrl('basket'));
    }

    public function down(BasketRow $basket_row){

        if(isset($basket_row)){
            $basket_row->setQuantity($basket_row->getQuantity()-1)->setSubtotal();
            $basket = $basket_row->getBasketId();
            $basket->setTotal($basket->getTotal()-$basket_row->getProductId()->getPrice());
            $this->em->persist($basket_row);
            $this->em->persist($basket);
            $this->em->flush();
            if($basket_row->getQuantity()==0){
                $this->removefrombasket($basket_row);
            }
        }

        return $this->redirect($this->generateUrl('basket'));
    }
}
