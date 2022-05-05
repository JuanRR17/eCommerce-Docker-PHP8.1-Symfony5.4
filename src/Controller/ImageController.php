<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends AbstractController
{
    private $em;
    private $request;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request,
    ){
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
    }

    public function remove_image(Image $image=null): Response
    {
        if(!$image){
          $imageId=$this->request->get('id');
          throw $this->createNotFoundException('The Image with id "'.$imageId.'" doesn\'t exist.');
      }
        if($image){
            $this->em->remove($image);
            $this->em->flush();

            //Remove file from the system
            unlink($image->getPath().$image->getName());
            //Check directory is empty
            function dir_is_empty($dir) {
                $handle = opendir($dir);
                while (false !== ($entry = readdir($handle))) {
                  if ($entry != "." && $entry != "..") {
                    closedir($handle);
                    return false;
                  }
                }
                closedir($handle);
                return true;
              }
              //Remove directory from system
              if(dir_is_empty($image->getPath())){
                rmdir($image->getPath());
              }            
        }
        return $this->redirect($this->generateUrl('editProduct', ['id'=> $image->getProduct()->getId()]));
    }   

    public function set_default(Image $image=null)
    {
        if(!$image){
          $imageId=$this->request->get('id');
          throw $this->createNotFoundException('The Image with id "'.$imageId.'" doesn\'t exist.');
        }
        $images=$this->em->getRepository('App\Entity\Image')->findByProduct($image->getProduct());
        foreach($images as $img){
            $img->setIsDefault(false);
        }
        $image->setIsDefault(true);
        $this->em->flush();
        return $this->redirect($this->generateUrl('editProduct', ['id'=> $image->getProduct()->getId()]));
    }
}
