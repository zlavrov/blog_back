<?php

namespace App\Controller;

use App\Entity\Article;
use App\Model\Out\Article\ArticleOut;
use App\Model\Out\Article\ArticleListOut;
use App\Model\In\Article\ArticleCreateIn;
use App\Model\In\Article\ArticleUpdateIn;

use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ArticleController extends AbstractController 
{
    private $autoMapper;

    private $articleRepository;

    private $entityManager;

    public function __construct(AutoMapperInterface $autoMapper, EntityManagerInterface $entityManager)
    {
        $this->autoMapper = $autoMapper;
        $this->entityManager = $entityManager;
        $this->articleRepository = $entityManager->getRepository(Article::class);
    }

    #[Route('/api/article', name: 'create_article', methods: ['POST'])]
    #[ParamConverter("articleCreateIn", converter: "fos_rest.request_body")]
    public function createArticle(ArticleCreateIn $articleCreateIn): JsonResponse
    {
        $article = $this->autoMapper->mapToObject($articleCreateIn, new Article());
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        
        return new JsonResponse([
            'status' => true,
            'message' => [
                'class' => 'ArticleController',
                'method' => 'createArticle',
                'content' => [
                    'Create_Row' => $article->getId()
                ]
            ]
        ]);
    }

    #[Route('/api/article/{id}', name: 'update_article', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    #[ParamConverter("articleUpdateIn", converter: "fos_rest.request_body")]
    public function updateArticle(ArticleUpdateIn $articleUpdateIn, $id): JsonResponse
    {
        $localArticle = $this->articleRepository->find($id);
        $article = $this->autoMapper->mapToObject($articleUpdateIn, $localArticle);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => true,
            'message' => [
                'class' => 'ArticleController', 
                'method' => 'updateArticle', 
                'content' => [
                    'Update_Row' => $article->getId()
                ]
            ]
        ]);
    }

    #[Route('/api/article/{id}', name: 'get_article_by_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getArticleById($id): JsonResponse
    {
        $localArticle = $this->articleRepository->find($id);
        $article = $this->autoMapper->map($localArticle, ArticleOut::class);

        return new JsonResponse([
            'status' => true,
            'message' => [
                'class' => 'ArticleController', 
                'method' => 'getArticleById',
                'content' => [
                    'article' => $article
                ]
            ]
        ]);
    }

    #[Route('/api/article/list', name: 'get_article_list', methods: ['GET'])]
    public function getArticleList(): JsonResponse
    {
        $localArticle = $this->articleRepository->findAll();
        $article = $this->autoMapper->mapMultiple($localArticle, ArticleListOut::class);

        return $this->json([
            'status' => true,
            'message' => [
                'class' => 'ArticleController', 
                'method' => 'getArticleList',
                'content' => [
                    'article' => $article
                ]
            ]
        ]);
    }

    #[Route('/api/article/{id}', name: 'delete_article', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteArticle($id): JsonResponse
    {
        $localArticle = $this->articleRepository->find($id);
        $this->entityManager->remove($localArticle);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => true,
            'message' => [
                'class' => 'ArticleController',
                'method' => 'deleteArticle',
                'content' => [
                    'Delete_Row' => $id
                ]
            ]
        ]);
    }
}
