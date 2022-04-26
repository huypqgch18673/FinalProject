<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('',name:'order_index')]
    public function orderIndex(){

        $order = $this->getDoctrine()->getRepository(Order::class)->findAll();
        $orderItems = $this->getDoctrine()->getRepository(OrderItem::class)->findAll();


        return $this->render("order/index.html.twig",[
            'orders' => $order,
            'orderItems' => $orderItems
            
        ]);
    }
    #[Route('/add',name:'order_add')]
    public function categoryAdd(Request $request)
    {   
        $order= new Order;
        $form = $this->createForm(OrderType::class,$order);
        $form ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($order);
            $manager->flush();
            return $this->redirectToRoute("order_index");
        }
        return $this->renderForm("order/add.html.twig",
        [
            'orderForm' =>$form
        ]);

    }
    
}
