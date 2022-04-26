<?php

namespace App\Controller;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('',name:'category_index')]
    public function categoryIndex(){

        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render("category/index.html.twig",[
            'category' => $category
        ]);
    }
    #[Route('/detail/{id}',name:'category_detail')]
    public function categoryDetail($id){
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if($category == null){
            $this->addFlash("Error","Category not found !");
            return $this->redirectToRoute("category_index");
        }
        return $this->render("category/detail.html.twig",
        [
            'category' => $category
        ]);
    }

    #[Route('delete/{id}',name:'category_delete')]
    public function categoryDelete($id) {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        // if($category == null)
        // {
        //     $this->addFlash("Error","Category is not found !!");
        // }
        // else if (count( $category->getCategory()) !=0) {
        //     $this->addFlash("Error","Can not delete Category !");
        // }
        // else {
            $manager= $this->getDoctrine()->getManager();
            $manager->remove($category);
            $manager->flush();
            $this->addFlash("Success","Delete author succeed !");
        // }
        return $this->redirectToRoute("category_index");
    }

    #[Route('/add',name:'category_add')]
    public function categoryAdd(Request $request)
    {
        $category= new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute("category_index");
        }
        return $this->renderForm("category/add.html.twig",
        [
            'categoryForm' =>$form
        ]);

    }

    #[Route('/edit{id}',name:'category_edit')]
    public function categoryEdit(Request $request,$id){
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form= $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute("category_index");
        }
        return $this->renderForm("category/edit.html.twig",
        [
            'categoryForm'=>$form
        ]);
    }
}