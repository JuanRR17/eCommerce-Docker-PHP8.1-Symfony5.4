<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
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
            $new_category = new Category();
            $new_category->setName($this->request->get('name'));
            
            $this->em->persist($new_category);
            $this->em->flush();

            $message = "Category created successfully";
        }
        
        return $this->render('category/manageCat.html.twig',[
            'message' => $message
        ]);
    }

    public function edit(Category $category=null): Response
    {
        if(!$category){
            $categoryId=$this->request->get('id');
            throw $this->createNotFoundException('The Category with id "'.$categoryId.'" doesn\'t exist.');
        }
        $edit_cat=$this->em->getRepository('App\Entity\Category')->findOneBy(['id' => $category]);
        $message="";

        if($this->request->getMethod() == 'POST'){
            $category->setName($this->request->get('name'));
            $this->em->persist($category);
            $this->em->flush();

            $message = "Category modified successfully";
        }
        
        return $this->render('category/manageCat.html.twig',[
            'message' => $message,
            'edit_cat' => $edit_cat
        ]);
    }

    public function remove(Category $category=null): Response
    {
        if(!$category){
            $categoryId=$this->request->get('id');
            throw $this->createNotFoundException('The Category with id "'.$categoryId.'" doesn\'t exist.');
        }
        $message="";
        if($category){
            $message = $category->getName()." deleted successfully";
            $this->em->remove($category);
            $this->em->flush();

        }else{
            $message = "Category couldn't be deleted";
        }
        
        return $this->render('category/manageCat.html.twig',[
            'message' => $message,
        ]);
    }

    public function showCategory(Category $category=null): Response
    {
        if(!$category){
            $categoryId=$this->request->get('id');
            throw $this->createNotFoundException('The Category with id "'.$categoryId.'" doesn\'t exist.');
        }
        if($category){
            $category_products = $this->em->getRepository('App\Entity\Product')
                                ->createQueryBuilder('p')
                                ->andWhere("p.category = :category")
                                ->setParameter('category', $category->getId())
                                ->orderBy('p.id', 'DESC')
                                ->getQuery()
                                ->execute();
            
        }

        return $this->render('category/showCategory.html.twig',[
            'category' => $category,
            'category_products' => $category_products
        ]);
    }
}
