<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Entity\Order;
use App\Form\VoitureType;
use App\Repository\CarRepository;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin', )]
class AdminController extends AbstractController
{
    //Modifier voiture
    //Ajouter voiture
    #[Route('/car/modifier/{id}', name:"modif_car")]
    #[Route('/car/add', name: 'add_car')]
    public function form(Request $req, EntityManagerInterface $manager, Car $car = null, SluggerInterface $slugger): Response
    {
        if($car == null)
        {
            $car =  new Car;
        }
        $form = $this->createForm(VoitureType::class, $car);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) 
        {
            /*Traitement de l'image*/
            
            $image = $form->get('picture')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }
                $car->setPicture($newFilename);
            }
            
            $manager->persist($car);
            $manager->flush();
            return $this->redirectToRoute('gestion_car');
        }

        return $this->render('admin/car/form.html.twig', [
            'formCar' => $form,
            'editMode' => $car->getId() !== null
        ]);
        
    }
    // Gestion voiture
    #[Route('/car/gestion', name:'gestion_car')]
    public function gestion(CarRepository $repo) : Response
    {
        $cars = $repo->findAll();
        return $this->render('admin/car/gestion.html.twig', [
            'cars' => $cars,
        ]);
    }
    #[Route('/car/delete/{id}', name: 'delete_car')]
    public function delete(Car $car,
    EntityManagerInterface $manager)
    {
       $manager->remove($car);
       $manager->flush();
       return $this->redirectToRoute('gestion_car');
    }
    #[Route('/user/gestion', name: 'gestion_user')]
    public function user(UserRepository $repo)
    {
        $users = $repo->findAll();
        return $this->render('admin/user/gestion.html.twig', [
            'users' => $users
        ]);
    }
    #[Route('/user/delete/{id}', name: 'delete_user')]
    public function delete_user(User $user,
    EntityManagerInterface $manager)
    {
       $manager->remove($user);
       $manager->flush();
       return $this->redirectToRoute('gestion_user');
    }
    #[Route('/user/modifier/{id}', name:"modif_user")]
    #[Route('/user/add', name: 'add_user')]
    public function formUser(Request $req, EntityManagerInterface $manager, User $user = null, SluggerInterface $slugger): Response
    {
        if($user == null)
        {
            $user =  new User;
        }
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) 
        {            
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('gestion_user');
        }

        return $this->render('admin/user/formuser.html.twig', [
            'formUser' => $form,
            'editMode' => $user->getId() !== null
        ]);
    }
    #[Route(path: '/order/gestion', name: 'order_gestion')]
    public function order(OrderRepository $repo)
    {
        $orders = $repo->findAll();
        return $this->render('/admin/user/gestionorder.html.twig', [
            'orders' => $orders
        ]);
    }
}