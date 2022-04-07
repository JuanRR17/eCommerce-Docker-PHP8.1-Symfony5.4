<?php

namespace App\Controller;

use App\Entity\Brand;
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

    public function index(): Response
    {
        return $this->render('brand/index.html.twig', [
            'controller_name' => 'BrandController',
        ]);
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
            //return $this->redirect($this->generateUrl('index'));
        }
        $brands=$this->em->getRepository('App:Brand')->findBy([], ['name' => 'ASC']);
        
        return $this->render('brand/manageBrand.html.twig',[
            'message' => $message,
            'brands' => $brands
        ]);
    }

    public function edit(Brand $brand): Response
    {
        $edit_brand=$this->em->getRepository('App:Brand')->findOneBy(['id' => $brand]);
        $message="";
        if($this->request->getMethod() == 'POST'){
            $brand->setName($this->request->get('name'));
            $this->em->persist($brand);
            $this->em->flush();

            $message = "Brand modified successfully";
            return $this->redirect($this->generateUrl('manageBrands'));
        }
        
        return $this->render('brand/editBrand.html.twig',[
            'message' => $message,
            'edit_brand' => $edit_brand
        ]);
    }

    public function remove(Brand $brand): Response
    {

        $message="";
        if($brand){
            $this->em->remove($brand);
            $this->em->flush();

            $message = "Brand deleted successfully";
            return $this->redirect($this->generateUrl('manageBrands'));

        }else{
            $message = "Brand couldn't be deleted";
        }
        $brands=$this->em->getRepository('App:Brand')->findBy([], ['name' => 'ASC']);

        return $this->render('brand/manageBrand.html.twig',[
            'message' => $message,
            'brands' => $brands
        ]);
    }
}
