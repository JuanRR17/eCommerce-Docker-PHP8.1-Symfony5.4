<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends AbstractController
{
    private $em;
    private $request;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request
    ){
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
    }

    public function manage(): Response
    {
        $message="";
        if($this->request->getMethod() == 'POST'){
            $new_brand = new Brand();
            $new_brand->setName($this->request->get('name'));
            
            $this->em->persist($new_brand);
            $this->em->flush();

            $message = "Brand created successfully";
        }
        
        return $this->render('brand/manageBrand.html.twig',[
            'message' => $message,
        ]);
    }

    public function edit(Brand $brand=null): Response
    {
        if(!$brand){
            $brandId=$this->request->get('id');
            throw $this->createNotFoundException('The Brand with id "'.$brandId.'" doesn\'t exist.');
        }
        $edit_brand=$this->em->getRepository('App\Entity\Brand')->findOneBy(['id' => $brand]);
        $message="";
        if($this->request->getMethod() == 'POST'){
            $brand->setName($this->request->get('name'));
            $this->em->persist($brand);
            $this->em->flush();

            $message = "Brand modified successfully";
        }
        
        return $this->render('brand/manageBrand.html.twig',[
            'message' => $message,
            'edit_brand' => $edit_brand
        ]);
    }

    public function remove(Brand $brand=null): Response
    {
        if(!$brand){
            $brandId=$this->request->get('id');
            throw $this->createNotFoundException('The Brand with id "'.$brandId.'" doesn\'t exist.');
        }
        $message="";
        if($brand){
            $message = $brand->getName()." deleted successfully";
            $this->em->remove($brand);
            $this->em->flush();
        }else{
            $message = "Brand couldn't be deleted";
        }

        return $this->render('brand/manageBrand.html.twig',[
            'message' => $message,
        ]);
    }

    public function showBrand(Brand $brand=null): Response
    {
        if(!$brand){
            $brandId=$this->request->get('id');
            throw $this->createNotFoundException('The Brand with id "'.$brandId.'" doesn\'t exist.');
        }
        if($brand){
            $brand_products = $this->em->getRepository('App\Entity\Product')
            ->createQueryBuilder('p')
            ->andWhere("p.brand = :brand")
            ->setParameter('brand', $brand->getId())
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->execute();

        }
        return $this->render('brand/showBrand.html.twig',[
            'brand' => $brand,
            'brand_products' => $brand_products
        ]);
    }
}
