<?php

namespace App\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BrandExtension extends AbstractExtension
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
            new TwigFunction('getBrands',[$this,'getBrands'])
        ];
    }

    public function getBrands($limit="8")
    {
        return  $this->em->getRepository('App:Brand')->findBy([],['name' => 'ASC'],$limit);
    }
}