<?php

namespace App\Controller;

use App\Admin\OrderStatus;
use App\Entity\Basket;
use App\Entity\BasketRow;
use App\Entity\Order;
use App\Entity\OrderRow;
use App\Entity\User;
use App\Form\OrderType;
use App\Form\StatusType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderController extends AbstractController
{
    private $em;
    private $request;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request
    )
    {
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
    }

    public function index(Request $request): Response
    {
        $orders = $this->em->getRepository('App:Order')->findAll();

        $ordersWithStatus=[];
        foreach($orders as $order){
            $this->updateOrderStatus($request, $order);
            $statusForm=$this->createForm(StatusType::class,$order);

            $ordersWithStatus[]=[
                'order'=>$order,
                'statusForm'=>$statusForm->createView()
            ];
        }
            return $this->render('order/manageOrders.html.twig', [
                'orders' => $ordersWithStatus,
            ]);
    }

    public function userIndex(UserInterface $user=null): Response
    {
        if(!$user){
            $userId=$this->request->get('id');
            throw $this->createNotFoundException('The User with id "'.$userId.'" doesn\'t exist.');
        }
        if($user){
            $orders = $this->em->getRepository('App:Order')->findBy(
                ['user'=> $user]
            );
        }
        return $this->render('order/user_orders_index.html.twig', [
            'orders' => $orders
        ]);
    }

    public function makeOrder(
        Request $request,
        ManagerRegistry $doctrine,
        Basket $basket=null): Response
    {
        if(!$basket){
            $basketId=$this->request->get('id');
            throw $this->createNotFoundException('The Basket with id "'.$basketId.'" doesn\'t exist.');
        }
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

        //Add Rows from Basket to Order
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
        $form->handleRequest($request);

        //Validate form
        if($form->isSubmitted() && $form->isValid()){
            //Auto-completing the user fields before saving
            $order->setCreatedAt(new \DateTimeImmutable("now"));
            $order->setStatus(OrderStatus::status['PENDING']);
            
            //Save order in database
            $em=$doctrine->getManager();
            //Persist Order
            $em->persist($order);
            //Persist all OrderRows
            foreach($order->getOrderRows() as $orderRow){
                $em->persist($orderRow);
            }
            $em->flush();

            //Update Stock
            $this->forward("App\Controller\ProductController::updateStock",[
                'order' => $order
            ]);
            
            //Empty Basket after flushing the Order
            $this->forward("App\Controller\BasketController::emptyBasket");
            
            return $this->redirect($this->generateUrl('orderConfirmation', ['id' => $order->getId()]));
        }
        return $this->render('order/orderForm.html.twig', [
            'form' => $form->createView(),
            'order' => $order,     
        ]);
    }

    public function orderConfirmation(Order $order=null): Response
    {
        if(!$order){
            $orderId=$this->request->get('id');
            throw $this->createNotFoundException('The Order with id "'.$orderId.'" doesn\'t exist.');
        }
        $confirmation=true;
        return $this->render('order/orderDetails.html.twig', [
            'order' => $order,    
            'confirmation' => $confirmation 
        ]);
    }

    public function showDetails(Order $order=null): Response
    {
        if(!$order){
            $orderId=$this->request->get('id');
            throw $this->createNotFoundException('The Order with id "'.$orderId.'" doesn\'t exist.');
        }
        return $this->render('order/orderDetails.html.twig', [
            'order' => $order,     
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order=null):Response
    {
        if(!$order){
            $orderId=$this->request->get('id');
            throw $this->createNotFoundException('The Order with id "'.$orderId.'" doesn\'t exist.');
        }
        $statusForm=$this->createForm(StatusType::class,$order);
    
        //Handle search
        $statusForm->handleRequest($request);

        //Update Status of Order in the Database
        if ($statusForm->isSubmitted() && $statusForm->isValid()) {
            $this->em->persist($order);
            $this->em->flush();
        }
        return $this->redirect($this->generateUrl('manageOrders'));
    }

    public function makeOrderFromLogin(
        Request $request,
        ManagerRegistry $doctrine,
        User $user
    ){
        $userBasket=$this->em->getRepository('App:Basket')->findOneBy(
            ['userid' => $user]
        );

        $noUserBasketRows=$this->em->getRepository('App:BasketRow')->findBy(
            ['userid' => null]
        );

        //Add Rows from No User Basket to User Basket
        foreach($noUserBasketRows as $noUserBasketRow){
            $rowProduct = $this->em->getRepository('App:Product')->findOneById([
                'id' => $noUserBasketRow->getProductId()
            ]); 
            
            $newBasketRow = new BasketRow;
            $newBasketRow
                ->setQuantity($noUserBasketRow->getQuantity())
                ->setBasketId($userBasket->getId())
                ->setProductId($rowProduct)
                ->setSubtotal()
                ;
            $this->em->persist($newBasketRow);
            $this->em->flush();
            $userBasket->addBasketRow($rowProduct);

        }

        $this->makeOrder($request, $doctrine, $userBasket);

    }
}
