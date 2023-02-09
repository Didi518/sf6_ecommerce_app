<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie', name: 'categories_')]
class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Category $category, ProductRepository $productRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $products = $productRepository->findProductPaginated($page, $category->getSlug(), 4);

        return $this->render('categories/list.html.twig', compact('category', 'products'));
    }
}