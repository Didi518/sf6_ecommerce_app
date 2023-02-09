<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/articles', name: 'admin_products_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }
    
    #[Route('/ajout', name: 'add')]
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/products/index.html.twig');
    }
    
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);   
        return $this->render('admin/products/index.html.twig');
    }
    
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);   
        return $this->render('admin/products/index.html.twig');
    }
}