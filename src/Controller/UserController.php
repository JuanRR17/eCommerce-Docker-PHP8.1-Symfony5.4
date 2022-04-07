<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, 
    ManagerRegistry $doctrine, User $user=null): Response
    {
        if(isset($user)){
            $edit=true;
       }else{
            $user=new User;
            $edit=null;
       }
        //Create form
        $form=$this->createForm(RegisterType::class, $user);

        //Fill the form
        $form->handleRequest($request);

        $registered = false;

        if($form->isSubmitted() && $form->isValid()){
            //Auto-completing the user fields before saving
            $user->setRole('ROLE_USER');
            $user->setCreatedAt(new \DateTimeImmutable("now"));

            //Hash password
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            //Save user in database
            $em=$doctrine->getManager();
            $em->persist($user);
            $em->flush();
            // var_dump($user);
            // return $this->redirectToRoute('index');
            $registered = true;
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'registered' => $registered,
            'edit' => $edit
        ]);
    }

}
