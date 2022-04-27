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
            //code xử lý tên ảnh và copy ảnh vào thư mục chứa project
            //B1: tạo biến $image để lấy ra tên image khi upload từ form
            $image = $brand->getImage();
            //B2: tạo tên file ảnh mới => đảm bảo tên ảnh là duy nhất
            $imgName = uniqid(); //unique id
            //B3: lấy ra đuôi (extension) của ảnh
            $imgExtension = $image->guessExtension();
            //Note: cần bỏ data type "string" trong hàm getImage() + setImage()
            //để biển $image thành object thay vì string
            //B4: ghép thành tên file ảnh hoàn thiện
            $imageName = $imgName . '.' . $imgExtension;
            //B5: copy ảnh vào thư mục chỉ định trong project
            try {
                $image->move(
                    $this->getParameter('brand_image'),
                    $imageName
                );
                //Note: cần set đường dẫn chứa ảnh trong file config/services.yaml  
            } catch (FileException $e) {
                throwException($e);
            }
            //B6: lưu tên ảnh vào DB
            $brand->setImage($imageName);
            //đẩy dữ liệu của book vào DB
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
        $brand = $registry->getRepository(Brand::class)->find($id);
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* 
           kiểm tra xem người dùng có upload ảnh mới
           cho book khi edit ảnh hay không
           nếu có thì chạy code xử lý ảnh giống phần Add 
           nếu không thì giữ nguyên ảnh cũ trong DB
           */
            $imageFile = $form['image']->getData();
            if ($imageFile != null) {
                $image = $brand->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('brand_image'),
                        $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $brand->setImage($imageName);
            }

            $manager = $registry->getManager();
            $manager->persist($brand);
            $manager->flush();
            $this->addFlash("Success", "Edit brand succeed !");
            return $this->redirectToRoute('brand_index');
        }
        return $this->renderForm(
            'brand/edit.html.twig',
            [
                'brandForm' => $form,
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

    #[Route('/search', name: 'brand_search')]
    public function search(Request $request, BrandRepository $brandRepository, ManagerRegistry $registry)
    {
        $countries = $registry->getRepository(Country::class)->findAll();
        $keyword = $request->get('title');
        $brands = $brandRepository->search($keyword);
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
