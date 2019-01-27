<?php

namespace App\Manager;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryManager
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Return the category who have the given in parameter id
     * @param int $id
     * @return Category|null
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Return all the categories
     * @return array|null
     */
    public function getAllCategory(): ?array
    {
        return $this->categoryRepository->findAll();
    }


}