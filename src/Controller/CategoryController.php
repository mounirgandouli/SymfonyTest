<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryFormType;
use App\Entity\Category; 
use App\Repository\CategoryRepository; 
use Doctrine\Persistence\ManagerRegistry;

class CategoryController extends AbstractController 
{
    #[Route('/categories', name: 'category_index')]
    public function index(CategoryRepository $repo): Response
    {
        $categories = $repo->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/categories/form', name: 'category_add')]
public function addCategory(ManagerRegistry $doctrine, Request $request): Response
{
    $category = new Category();
    $form = $this->createForm(CategoryFormType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('category_index'); // Redirect to the category index page.
    }

    return $this->render('category/form.html.twig', [
        'formA' => $form->createView(),
    ]);
}


#[Route('/categories/{id}/edit', name: 'category_edit')]
public function editCategory(ManagerRegistry $doctrine, Request $request, $id): Response
{
    $em = $doctrine->getManager();
    $category = $em->getRepository(Category::class)->find($id);

    if (!$category) {
        throw $this->createNotFoundException('Category not found');
    }

    $form = $this->createForm(CategoryFormType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        return $this->redirectToRoute('category_index'); // Redirect to the category index page.
    }

    return $this->render('category/edit.html.twig', [
        'form' => $form->createView(),
        'category' => $category,
    ]);
}


    #[Route('/categories/{id}/delete', name: 'category_delete')] 
    public function deleteCategory(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $category = $em->getRepository(Category::class)->find($id); 

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('category_show'); 
    }
}
