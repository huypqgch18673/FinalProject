<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    #[Route('/home', name: 'homepage')]
        public function homeIndex()
        {
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            return $this->render(
                'homepage/index.html.twig',
                [
                    'product'=> $products
                ]
            );
    }
}
