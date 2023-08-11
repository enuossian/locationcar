<?php

namespace App\Controller;


use DateTime;
use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    public function show($id, CarRepository $rep, Request $req, EntityManagerInterface $manager)
    
    { $order = new Order;
    
        $cars = $rep->find($id);
        $formOrder = $this->createForm(OrderType::class, $order);
        $formOrder->handleRequest($req);
        if($formOrder->isSubmitted() && $formOrder->isValid())
        {
            //dd($order);
            // Traitement de la date 
            $price = $cars->getPrice();
            $start = $order->getDateStart();
            $end = $order->getDateEnd();
            $interval = $start->diff($end);
            $days = $interval->days;

            $order->setRegistrationDate( new \DateTime())
                    ->setIdCar($cars)
                    ->setIdUser($this->getUser())
                    ->setTotalPrice($price * $days);

            $manager->persist($order);
            $manager->flush();
            return $this->redirectToRoute('app');
        }
            
        return $this->render('application/show.html.twig', [
            'car' => $cars,'formReserv' => $formOrder,
        ]);
    }
   
}