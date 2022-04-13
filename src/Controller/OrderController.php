<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\OrderRow;
use App\Entity\User;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        Basket $basket): Response
    {
        //Create and fill Order details
        $order=new Order();
        $user=$basket->getUserid();
        $order->setUser($user)
        ->setCost($basket->getTotal())
        ->setAddress($user->getAddress())
        ->setCity($user->getCity())
        ->setCountry($user->getCountry())
        ->setReceiverName($user->getName())
        ->setReceiverSurname($user->getSurname())
        ;

        $basketRows = $this->em->getRepository('App:BasketRow')->findBy([
            'basket_id' => $basket
        ]);

        foreach($basketRows as $basketRow){
            $rowProduct = $this->em->getRepository('App:Product')->findOneById([
                'id' => $basketRow->getProductId()
            ]);
            $newOrderRow= new OrderRow;
            $newOrderRow
                ->setQuantity($basketRow->getQuantity())
                ->setOrderId($order->getId())
                ->setProduct($rowProduct)
                ->setSubtotal()
            ;
            $order->addOrderRow($newOrderRow);
        }
        
        //Create form
        $form=$this->createForm(OrderType::class,$order);

        //Fill the form
        // $form->handleRequest($request);

        // if($form->isSubmitted() && $form->isValid()){


        //     //Save order in database
        //     $em=$doctrine->getManager();
        //     $em->persist($order);
        //     $em->flush();
        // }
        return $this->render('order/order.html.twig', [
            'form' => $form->createView(),
            'order' => $order,     
        ]);
    }
}
