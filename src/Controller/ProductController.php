<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProductFormType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/products/form', name: 'product_add')]
public function addProduct(ManagerRegistry $doctrine, Request $request): Response
{
    $product = new Product();
    $form = $this->createForm(ProductFormType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('product_index'); // Redirect to the product index page.
    }

    return $this->render('product/form.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/products/show', name: 'product_show')]
    public function showProduct(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        return $this->render('Product/productlist.html.twig', ['products' => $products]);
    }
    
    #[Route('/products/{id}/edit', name: 'product_edit')]
public function editProduct(ManagerRegistry $doctrine, Request $request, $id): Response
{
    $em = $doctrine->getManager();
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) {
        throw $this->createNotFoundException('Product not found');
    }

    $form = $this->createForm(ProductFormType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        return $this->redirectToRoute('product_show'); // Redirect to the product show page.
    }

    return $this->render('product/edit.html.twig', [
        'form' => $form->createView(),
        'product' => $product,
    ]);
}


    #[Route('/products/{id}/delete', name: 'product_delete')]
    public function deleteProduct(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('product_show');
    }
}
