<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\OrderItem;
use App\Form\OrderItemType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/orderItem')]
class OrderItemController extends AbstractController
{
    #[Route('', name: 'orderItem_index')]
    public function orderItemIndex()
    {
        $orderItems = $this->getDoctrine()->getRepository(OrderItem::class)->findAll();
        return $this->render('orderItem/index.html.twig',[
            'orderItems'=> $orderItems
            ]
        );
    }

    #[Route('/{id}',name:'orderItem_add')]
    public function addOrder(Request $request,$id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $order = new OrderItem;
        $form = $this->createForm(OrderItemType::class, $order);
        $form ->handleRequest($request);
        $order->setProduct($product);
        if($order!==null)
        {
            if($order->getQuantity()<=$product->getQuantity())
            {
                $product->setQuantity($product->getQuantity()- $order->getQuantity());
            }
            else{
                $this->addFlash("Error","You try again!!!");
                return $this->redirectToRoute("orderItem_add");
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($order);
        $manager->flush();
        return $this->redirectToRoute("orderItem_index");
        }
        return $this->renderForm("orderItem/add.html.twig",[
            'product' =>$product,
            'FormOder'=> $form
        ]);
    }
    #[Route('delete{id}', name: 'orderItems_delete')]
    public function deleteCart($id){
        $temp = $this->getDoctrine()->getRepository(OrderItem::class)->find($id)->getProduct()->getId();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($temp);
        $product->setQuantity($product->getQuantity()+ $this->getDoctrine()->getRepository(OrderItem::class)->find($id)->getQuantity());
        $orderItems = $this->getDoctrine()->getRepository(OrderItem::class)->find($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($orderItems);
        $manager->flush();
        
        return $this->redirectToRoute("orderItem_index");
    }
}
