<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageController extends AbstractController
{
    private $em;

    public function __construct(
        EntityManagerInterface $em,
    ){
        $this->em = $em;
    }

    public function remove_image(Image $image)
    {
        if($image){
            $this->em->remove($image);
            $this->em->flush();
        }
        return $this->redirect($this->generateUrl('editProduct', ['id'=> $image->getProduct()->getId()]));
    }   

    public function set_default(Image $image)
    {
        $images=$this->em->getRepository('App:Image')->findByProduct($image->getProduct());
        foreach($images as $img){
            $img->setIsDefault(false);
        }
        $image->setIsDefault(true);
        $this->em->flush();
        return $this->redirect($this->generateUrl('editProduct', ['id'=> $image->getProduct()->getId()]));
    }
}
