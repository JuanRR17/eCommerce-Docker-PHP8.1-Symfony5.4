<?php

namespace App\Twig\Extension;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasketExtension extends AbstractExtension
{
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ){
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('statsBasket', [$this, 'statsBasket'])
        ];
    }

    public function statsBasket(?User $user){
        $stats = array(
            'count' => 0,
            'total' => 0
        );
        if($user){     
            $basket = $this->em->getRepository('App:Basket')->findOneBy([
                'userid' => $user->getId()]); 
                if($basket){
			        $stats['count'] = count($basket->getBasketRows());
			
                    $stats['total'] = $basket->getTotal();
                }
        }elseif($basketNoUser = $this->em->getRepository('App:Basket')->findOneBy([
                'userid'=>null])){
                    $stats['count'] = count($basketNoUser->getBasketRows());
			
                    $stats['total'] = $basketNoUser->getTotal();  
        }
        return $stats;
    }
}