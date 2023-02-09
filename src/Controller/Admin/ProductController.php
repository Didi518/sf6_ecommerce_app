<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/articles', name: 'admin_products_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }
    
    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $product = new Product();

        $productForm = $this->createForm(ProductFormType::class, $product);
        $productForm->handleRequest($request);
        
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $images = $productForm->get('images')->getData();

            foreach ($images as $image) {
                $folder = 'products';
                $file = $pictureService->add($image, $folder, 300, 300);
                $img = new Image();
                $img->setName($file);
                $product->addImage($img);
            } 

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Article ajouté avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }
    
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product); 

        $productForm = $this->createForm(ProductFormType::class, $product);
        $productForm->handleRequest($request);
        
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $images = $productForm->get('images')->getData();

            foreach ($images as $image) {
                $folder = 'products';
                $file = $pictureService->add($image, $folder, 300, 300);
                $img = new Image();
                $img->setName($file);
                $product->addImage($img);
            }
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product' => $product
        ]);
    }
    
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);   
        return $this->render('admin/products/index.html.twig');
    }

    #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Image $image, Request $request, EntityManagerInterface $manager, PictureService $pictureService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $nom = $image->getName();

            if ($pictureService->delete($nom, 'products', 300, 300)) {
                $manager->remove($image);
                $manager->flush();

                return new JsonResponse(['success' => true], 200);
            }

            return new JsonResponse(['error' => 'Erreur lors de la suppression'], 400);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}