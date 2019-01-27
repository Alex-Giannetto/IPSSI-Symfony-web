<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Manager\CategoryManager;
use App\Manager\VideoManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * This page show the list of category
     * @Route("/admin/categories", name="admin_categoryList")
     */
    public function categories(CategoryManager $categoryManager)
    {
        $categories = $categoryManager->getAllCategory();
        return $this->render('category/categoryList.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * This page show all the detail + video (which have this category)
     * @Route("/admin/categories/show/{id}", name="admin_category")
     */
    public function category(CategoryManager $categoryManager, int $id)
    {
        $category = $categoryManager->getCategoryById($id);
        return $this->render('category/category.html.twig', [
            'category' => $category,
            'videos' => $category->getVideos()
        ]);
    }

    /**
     * This page show a form to modify the category information
     * @Route("/admin/categories/modify/{id}", name="admin_modifyCategory")
     */
    public function modifyCategory(
        CategoryManager $categoryManager,
        EntityManagerInterface $entityManager,
        Request $request,
        int $id
    ) {
        $category = $categoryManager->getCategoryById($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'The ' . $category->getTitle() . '\'s informations has been modified');
            return $this->redirectToRoute('admin_category', ['id' => $id]);
        }
        return $this->render('security/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This page show a form to add a category
     * @Route("/admin/categories/add/", name="admin_addCategory")
     */
    public function addCategory(
        CategoryManager $categoryManager,
        EntityManagerInterface $entityManager,
        Request $request
    ) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'The category has been added');
            return $this->redirectToRoute('admin_category', ['id' => $category->getId()]);
        }
        return $this->render('category/form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * This page delete a category
     * @Route("/admin/categories/delete/{id}", name="admin_deleteCategory")
     */
    public function removeCategory(
        CategoryManager $categoryManager,
        EntityManagerInterface $entityManager,
        Request $request,
        int $id
    ) {
        $category = $categoryManager->getCategoryById($id);

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'The ' . $category->getTitle() . ' category has been deleted');
        return $this->redirectToRoute('admin_categoryList');
    }

    /**
     * This page show contains all video for a category
     * @Route("/categories/{id}", name="videoByCategories")
     */
    public function videoByCategories(
        VideoManager $videoManager,
        CategoryManager $categoryManager,
        int $id
    ) {
        $category = $categoryManager->getCategoryById($id);
        $videos = $category->getVideos();

        return $this->render('category/home.html.twig', [
            'category' => $category->getTitle(),
            'videos' => $videos
        ]);
    }


}
