<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie', name: 'categories_')]
class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Category $category): Response
    {
        $products = $category->getProducts();
        return $this->render('categories/list.html.twig', compact('category', 'products'));
    }
}