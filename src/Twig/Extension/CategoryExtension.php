<?php

namespace App\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryExtension extends AbstractExtension
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
            new TwigFunction('getCategories',[$this,'getCategories'])
        ];
    }

    public function getCategories(int $limit=null)
    {
        return  $this->em->getRepository('App\Entity\Category')->findBy([],['name' => 'ASC'],$limit);
    }
}