<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/country')]
class CountryController extends AbstractController
{
    #[Route('/', name: "country_index")]
    public function countryIndex(ManagerRegistry $registry)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        return $this->render(
            "country/index.html.twig",
            [
                'countries' => $countries
            ]
        );
    }

    #[Route('/detail/{id}', name: 'country_detail')]
    public function countryDetail(ManagerRegistry $registry, $id)
    {
        $country = $registry->getRepository(Country::class)->find($id);
        if ($country == null) {
            $this->addFlash("Error", "This country not exsist !");
            return $this->redirectToRoute("country_index");
        }
        return $this->render(
            "country/detail.html.twig",
            [
                'country' => $country
            ]
        );
    }

    #[Route('/delete/{id}', name: 'country_delete')]
    public function countryDelete(ManagerRegistry $registry, $id)
    {
        $country = $registry->getRepository(Country::class)->find($id);
        if ($country == null) {
            $this->addFlash("Error", "Country not not exsist !");;
        }
        //check xem country cần xóa có tồn tại tối thiểu 1 brand hay không
        //nếu có thì không cho xóa và thông báo lỗi
        else if (count($country->getBrands()) >= 1) {
            $this->addFlash("Error", "Can not delete this country !");
        } else {
            $manager = $registry->getManager();
            $manager->remove($country);
            $manager->flush();
            $this->addFlash("Success", "Delete this country succeed !");
        }
        return $this->redirectToRoute("country_index");
    }

    #[Route('/add', name: 'country_add')]
    public function countryAdd(Request $request, ManagerRegistry $registry)
    {
        $country = new Country;
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $registry->getManager();
            $manager->persist($country);
            $manager->flush();
            $this->addFlash("Success", "Add country succeed !");
            return $this->redirectToRoute('country_index');
        }
        return $this->renderForm(
            'country/add.html.twig',
            [
                'countryForm' => $form
            ]
        );
    }

    #[Route('/edit/{id}', name: 'country_edit')]
    public function countryEdit(Request $request, ManagerRegistry $registry, $id)
    {
        $country = $registry->getRepository(Country::class)->find($id);
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $registry->getManager();
            $manager->persist($country);
            $manager->flush();
            $this->addFlash("Success", "Edit this country succeed !");
            return $this->redirectToRoute('country_index');
        }
        return $this->renderForm(
            'country/edit.html.twig',
            [
                'countryForm' => $form
            ]
        );
    }
}
