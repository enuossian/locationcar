<?php

namespace App\Controller;


use Doctrine\ORM\Mapping\Entity;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ApplicationController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(CarRepository $rep)
    {
        $cars = $rep->findAll();
        return $this->render('application/index.html.twig', [
            'car' => $cars
        ]);
    }
    #[Route('/car/show/{id}', name: 'show_car')]
    public function show($id, CarRepository $rep)
    {
        $cars = $rep->find($id);    
        return $this->render('application/show.html.twig', [
            'car' => $cars,
        ]);
    }

}