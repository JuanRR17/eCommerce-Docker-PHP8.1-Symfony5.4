<?php

namespace App\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasketExtension extends AbstractExtension
{
    private $em;
    // private $user;

    public function __construct(
        EntityManagerInterface $em,
        // UserInterface $user
    ){
        $this->em = $em;
        // $this->user = $user;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('statsBasket', [$this, 'statsBasket'])
        ];
    }

    public function statsBasket(){
        // $basket = $this->em->getRepository('App:Basket')->findOneBy(['userid' => $this->user]);
        $basket = $this->em->getRepository('App:Basket')->findAll()[0];
        // dump($basket);
        // die();
        $stats = array(
            'count' => 0,
            'total' => 0
        );

        if(!empty($basket)){
			$stats['count'] = count($basket->getBasketRows());
			
            $stats['total'] = $basket->getTotal();
        }
		
        return $stats;
    }
}