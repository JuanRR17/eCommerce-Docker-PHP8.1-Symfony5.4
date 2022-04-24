<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductController extends AbstractController
{
    private $em;
    private $request;
    private $projectDir;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request,
        $projectDir
    ){
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
        $this->projectDir = $projectDir;
    }

    public function index(Request $request=null): Response
    {
        $products = $this->em->getRepository(Product::class)->findBy([],['id' => 'DESC']);

        // searchBar request handling
        if($request->request->count() > 0){
            $search=$request->request->get('search')['searchBar'];

            //Search within Products
            $matches=[];
            foreach($products as $product){
                if(
                    str_contains($product->getModel(), $search)
                    ||
                    str_contains($product->getSpecifications(), $search)
                    ||
                    str_contains($product->getColour(), $search)
                    ){
                    $matches[]=$product;
                }
            }
            return $this->render('product/index.html.twig', [
                'products' => $matches,
                'search' => $search
            ]);
        }
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    public function searchBar(Request $request=null):Response
    {
        //Create search Bar Form
        $search = [];

        $form=$this->createForm(SearchType::class,$search);
        //Handle search
        $form->handleRequest($request);

        //If there is a search, redirect to index
        if ($form->isSubmitted() && $form->isValid()) {
            $this->redirect($this->generateUrl('index'));
        }
                   
        return $this->render('includes/searchBar.html.twig', [
            'form' => $form->createView()
            ]);
    }

    public function manage(string $message=null): Response
    {
        $products = $this->em->getRepository(Product::class)->findBy([],['id' => 'DESC']);

        return $this->render('product/manageP.html.twig', [
            'products' => $products,
            'message' => $message
        ]);
    }

    public function create(Product $product=null): Response
    {
        //Gather all categories and brands in the database
        //These will be passed to the view to the select input
       $categories = $this->em->getRepository('App:Category')->findBy([],['name' => 'ASC']);
       $brands = $this->em->getRepository('App:Brand')->findBy([],['name' => 'ASC']);
       $message="";
       $error=false;
       if(isset($product)){
            $newProduct = $product;
            $edit=true;
       }else{
            $newProduct = new Product();
            $edit=null;
       }

       if($this->request->getMethod() == 'POST'){
        
            //Check we get all required values
            if(empty($_POST['category']) || empty($_POST['brand']) || empty($_POST['model'])
            || empty($_POST['price']) || empty($_POST['stock']) || empty($_POST['colour'])
            ){
                $message = "Some required data is missing.";
            }

            $error_message=[];
            //Take values from the form
            $category = $this->em->getRepository('App:Category')->findOneById($this->request->get('category'));
            $brand = $this->em->getRepository('App:Brand')->findOneById($this->request->get('brand'));
            $model = $this->request->get('model');
            $specifications = $this->request->get('specifications');
            $price = $this->request->get('price');
            $stock = $this->request->get('stock');
            $offer = $this->request->get('offer');
            $colour = $this->request->get('colour');
            
            //VALIDATE FORM
            if(!empty($category)){
                $newProduct->setCategory($category);
            }else{
                $error_message['category'] = 'Please select a category.';
            }

            if(!empty($brand)){
                $newProduct->setBrand($brand);
            }else{
                $error_message['brand'] = 'Please select a brand.';
            }

            if(!empty($model)){
                $newProduct->setModel($model);
            }else{
                $error_message['model'] = 'Please input a model.';
            }

            if(!empty($colour)){
                $newProduct->setColour($colour);
            }else{
                $error_message['colour'] = 'Please input a colour.';
            }

            if(!empty($price)){
                if($price>0){
                    $newProduct->setPrice($price);
                }else{
                $error_message['price'] = 'Please input a price higher than 0.';
                }    
            }else{
                $error_message['price'] = 'Please input a price.';
            }

            // if(!empty($stock)){
            if($stock>=0){
                $newProduct->setStock($stock);
            }elseif($stock>0){
                $error_message['stock'] = 'Stock can\'t be negative';
            }else{
                $error_message['stock'] = 'Please input a stock.';
            }

            if($offer != null){
                if($offer<0){
                    $error_message['offer'] = 'Please input an offer higher than 0.';
                } else if($offer>100){
                    $error_message['offer'] = 'Please input an offer lower than 100.';
                } else if($offer==0){
                    $newProduct->setOffer(null);
                } else{
                    $newProduct->setOffer($offer);
                }
            }
            
            if($specifications != null){
                $newProduct->setSpecifications($specifications);
            } 

            if(empty($error_message)){
                //Send data to the database
                $this->em->persist($newProduct);
                $this->em->flush();

            //UPLOAD IMAGE

                $count = 0;

                function count_element($element){
                    if($element < 10){
                        $element_name = '0'.$element;
                    }else{
                        $element_name = $element;
                    }
                    return $element_name;
                };

                foreach($this->request->files->get('images') as $image) {

                    $count_name=count_element($count);
                    $count_product=count_element($newProduct->getId());
                    $uploadFolder = "{$this->projectDir}/public/assets/imgs/products/{$newProduct->getId()}/";

                    $image_formatted_name = $count_product.'-'.$count_name.'.'.$image->getClientOriginalExtension();

                    if(!is_dir($uploadFolder)){
                        mkdir($uploadFolder, 0777,true);
                    }

                    $db_images = $this->em->getRepository('App:Image')->findBy(['product' => $newProduct->getId()]);
                    if($db_images !== null){
                        //Create array with the images name.
                        $db_images_names = array();
                        foreach($db_images as $image_name){
                            $db_images_names[]=$image_name->getName();
                        }
                        while(in_array($image_formatted_name, $db_images_names)){
                            $count++;
                            if($count < 10){
                                $count_name = '0'.$count;
                            }else{
                                $count_name = $count;
                            }
                            $image_formatted_name = $newProduct->getId().'-'.$count_name.'.'.$image->getClientOriginalExtension();
                        }
                    }

                    $image->move($uploadFolder,$image_formatted_name);
                    $test=$this->em->getRepository('App:Image')->findByProduct($newProduct->getId());
                    $newImage = new Image();
                    $newImage
                        ->setName($image_formatted_name)
                        ->setProduct($newProduct)
                        ->setPath($uploadFolder)
                        ->setIsDefault(
                            in_array(true,array_map(
                            function($def){return $def->getIsDefault();},
                            $this->em->getRepository('App:Image')->findByProduct($newProduct->getId()))) 
                               ? false : true);
                    $this->em->persist($newImage);
                    
                    $count++;

                    $this->em->flush();
                }

                $message = $edit ? "Product modified successfully!!" : "Product created successfully!!";
            }else{
                $error=$error_message;
            }
        }
        //categories and brand are sent to the view to the select input
        return $this->render('product/createP.html.twig', [
            'categories' => $categories,
            'brands' => $brands,
            'message' => $message,
            'error' => $error,
            'newProduct' => $newProduct,
            'edit' => $edit
        ]);
    }

    public function remove(Product $product): Response
    {
        $message="";
        if($product){
            $message=$product->getBrand()->getName().' '.$product->getModel().' removed successfully!';
            $this->em->remove($product);
            $this->em->flush();
        }else{
            $message="Product couldn't be removed";    
        }
        return $this->manage($message);
    }

    public function detail(Product $product, $img_id): Response
    {    
        if($img_id != null){
            $main=$this->em->getRepository('App:Image')->findOneBy(['id'=> $img_id]);
        }else{
            $main=$this->em->getRepository('App:Image')->findOneBy(['product'=>$product->getId(),'isDefault'=>true]);
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'main' => $main
        ]);
    }  

    public function updateStock(Order $order)
    {
        $orderRows=$this->em->getRepository('App:OrderRow')->findBy([
            'order_id' => $order
        ]);

        foreach($orderRows as $orderRow){
            $rowProduct=$this->em->getRepository('App:Product')->findOneBy([
                'id' => $orderRow->getProduct()
            ]);
            $rowProduct->setStock(($rowProduct->getStock() - $orderRow->getQuantity()));
        }
    }
}
