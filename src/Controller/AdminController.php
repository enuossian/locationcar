<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\VoitureType;
use App\Repository\CarRepository;
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
    

}
