<?php

namespace App\Controller;

use App\Entity\Category;
use App\Model\Out\Category\CategoryOut;
use App\Model\Out\Category\CategoryListOut;
use App\Model\In\Category\CategoryCreateIn;
use App\Model\In\Category\CategoryUpdateIn;

use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CategoryController extends AbstractController
{
    private $autoMapper;

    private $categoryRepository;

    private $entityManager;

    public function __construct(AutoMapperInterface $autoMapper, EntityManagerInterface $entityManager)
    {
        $this->autoMapper = $autoMapper;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    #[Route('/api/category', name: 'create_category', methods: ['POST'])]
    #[ParamConverter("categoryCreateIn", converter: "fos_rest.request_body")]
    public function createCategory(CategoryCreateIn $categoryCreateIn): JsonResponse
    {
        $category = $this->autoMapper->mapToObject($categoryCreateIn, new Category());
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => ['Create_Row' => $category->getId()]]);
    }

    #[Route('/api/category/{id}', name: 'update_category', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    #[ParamConverter("categoryUpdateIn", converter: "fos_rest.request_body")]
    public function updateCategory(CategoryUpdateIn $categoryUpdateIn, $id): JsonResponse
    {
        $localCategory = $this->categoryRepository->find($id);
        if(!$localCategory) {
            return $this->json(['status' => false, 'message' => ['Update_Row' => "Row_" . $id . "_not_found"]]);
        }
        $category = $this->autoMapper->mapToObject($categoryUpdateIn, $localCategory);
        $this->entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => ['Update_Row' => $category->getId()]]);
    }

    #[Route('/api/category/{id}', name: 'get_category_by_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getCategoryById($id): JsonResponse
    {
        $localCategory = $this->categoryRepository->find($id);
        if(!$localCategory) {
            return $this->json(['status' => false, 'message' => ['Category' => "Row_" . $id . "_not_found"]]);
        }
        $category = $this->autoMapper->map($localCategory, CategoryOut::class);

        return new JsonResponse(['status' => true, 'message' => ['Category' => $category]]);
    }

    #[Route('/api/category/list', name: 'get_category_list', methods: ['GET'])]
    public function getCategoryList(): JsonResponse
    {
        $localCategories = $this->categoryRepository->findAll();
        if(!$localCategories) {
            return $this->json(['status' => false, 'message' => ['Categories' => "Rows_not_found"]]);
        }
        $categories = $this->autoMapper->mapMultiple($localCategories, CategoryListOut::class);
        
        return new JsonResponse(['status' => true, 'message' => ['Categories' => $categories]]);
    }

    #[Route('/api/category/{id}', name: 'delete_category', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteCategory($id): JsonResponse
    {
        $localCategory = $this->categoryRepository->find($id);
        if(!$localCategory) {
            return $this->json(['status' => false, 'message' => ['Delete_Row' => "Row_" . $id . "_not_found"]]);
        }
        $this->entityManager->remove($localCategory);
        $this->entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => ['Delete_Row' => $id]]);
    }
}
