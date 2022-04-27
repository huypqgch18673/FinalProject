<?php

namespace App\Controller;

use Exception;
use App\Entity\Brand;
use App\Entity\Country;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\throwException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/brand')]
class BrandController extends AbstractController
{
    #[Route('/', name: 'brand_index')]
    public function brandIndex(ManagerRegistry $registry)
    {
        $brands = $registry->getRepository(Brand::class)->findAll();
        $countries = $registry->getRepository(Country::class)->findAll();
        return $this->render(
            "brand/index.html.twig",
            [
                'brands' => $brands,
                'countries' => $countries
            ]
        );
    }

    #[Route('/detail/{id}', name: 'brand_detail')]
    public function bookDetail(ManagerRegistry $registry, $id)
    {
        $brand = $registry->getRepository(Brand::class)->find($id);
        $countries = $registry->getRepository(Country::class)->findAll();
        if ($brand == null) {
            $this->addFlash("Error", "Brand not exsist !");
            return $this->redirectToRoute("brand_index");
        }
        return $this->render(
            "brand/detail.html.twig",
            [
                'brand' => $brand,
                'countries' => $countries
            ]
        );
    }

    #[Route('/delete/{id}', name: 'brand_delete')]
    public function brandDelete(ManagerRegistry $registry, $id)
    {
        $brand = $registry->getRepository(Brand::class)->find($id);
        $countries = $registry->getRepository(Country::class)->findAll();
        if ($brand == null) {
            $this->addFlash("Error", "Brand not found !");
        } else {
            $manager = $registry->getManager();
            $manager->remove($brand);
            $manager->flush();
            $this->addFlash("Success", "Brand delete succeed !");
        }
        return $this->redirectToRoute(
            "brand_index",
            [
                'countries' => $countries
            ]
        );
    }

    #[Route('/add', name: 'brand_add')]
    public function brandAdd(Request $request, ManagerRegistry $registry)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        $brand = new Brand;
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $registry->getManager();
            $manager->persist($brand);
            $manager->flush();
            $this->addFlash("Success", "Add brand succeed !");
            return $this->redirectToRoute('brand_index');
        }
        return $this->renderForm(
            'brand/add.html.twig',
            [

                'brandForm' => $form,
                'countries' => $countries
            ]
        );
    }

    #[Route('/edit/{id}', name: 'brand_edit')]
    public function brandEdit(Request $request, ManagerRegistry $registry, $id)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        $brand = $registry->getRepository(Country::class)->find($id);
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $registry->getManager();
            $manager->persist($brand);
            $manager->flush();
            $this->addFlash("Success", "Edit brand succeed !");
            return $this->redirectToRoute('brand_index');
        }
        return $this->renderForm(
            'brand/edit.html.twig',
            [

                'bookForm' => $form,
                'countries' => $countries
            ]
        );
    }

    #[Route('/asc', name: 'brand_asc')]
    public function sortAsc(BrandRepository $brandRepository, ManagerRegistry $registry)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        $brands = $brandRepository->sortBrandAsc();
        return $this->render(
            "brand/index.html.twig",
            [
                'brands' => $brands,
                'countries' => $countries
            ]
        );
    }

    #[Route('/desc', name: 'brand_desc')]
    public function sortDesc(BrandRepository $brandRepository, ManagerRegistry $registry)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        $brands = $brandRepository->sortBrandDesc();
        return $this->render(
            "brand/index.html.twig",
            [
                'brands' => $brands,
                'countries' => $countries
            ]
        );
    }



    #[Route('/filter/{id}', name: 'brand_filter')]
    public function filter($id, ManagerRegistry $registry)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        $country = $registry->getRepository(Country::class)->find($id);
        $brands = $country->getBrands();
        return $this->render(
            "brand/index.html.twig",
            [
                'brands' => $brands,
                'countries' => $countries
            ]
        );
    }
}
