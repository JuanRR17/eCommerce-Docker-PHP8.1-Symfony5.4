<?php

namespace App\Controller;

use App\Entity\Basket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }
    public function index(Basket $basket): Response
    {
        // $basket = $this->em->getRepository('App:Basket')->findAll()[0];

        return $this->render('order/index.html.twig', [
            'basket' => $basket
        ]);
    }
}
