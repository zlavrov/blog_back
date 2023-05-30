<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Model\Out\Comment\CommentOut;
use App\Model\Out\Comment\CommentListOut;
use App\Model\In\Comment\CommentCreateIn;
use App\Model\In\Comment\CommentUpdateIn;

use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentController extends AbstractController
{
    private $autoMapper;

    private $commentRepository;

    private $entityManager;

    public function __construct(AutoMapperInterface $autoMapper, EntityManagerInterface $entityManager)
    {
        $this->autoMapper = $autoMapper;
        $this->entityManager = $entityManager;
        $this->commentRepository = $entityManager->getRepository(Comment::class);
    }

    #[Route('/api/comment', name: 'create_comment', methods: ['POST'])]
    #[ParamConverter("commentCreateIn", converter: "fos_rest.request_body")]
    public function createComment(CommentCreateIn $commentCreateIn): JsonResponse
    {
        $comment = $this->autoMapper->mapToObject($commentCreateIn, new Comment());
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => [
                'class' => 'CommentController', 
                'method' => 'createComment',
                'content' => [
                    'Create_Row' => $comment->getId()
                ]
            ]
        ]);
    }

    #[Route('/api/comment/{id}', name: 'update_comment', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    #[ParamConverter("commentUpdateIn", converter: "fos_rest.request_body")]
    public function updateComment(CommentUpdateIn $commentUpdateIn, $id): JsonResponse
    {
        $localComment = $this->commentRepository->find($id);
        $comment = $this->autoMapper->mapToObject($commentUpdateIn, $localComment);
        $this->entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => [
                'class' => 'CommentController', 
                'method' => 'updateComment', 
                'content' => [
                    'Update_Row' => $comment->getId()
                ]
            ]
        ]);
    }

    #[Route('/api/comment/{id}', name: 'get_comment_by_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getCommentById($id): JsonResponse
    {
        $localComment = $this->commentRepository->find($id);
        $comment = $this->autoMapper->map($localComment, CommentOut::class);

        return $this->json([
            'status' => true,
            'message' => [
                'class' => 'CommentController', 
                'method' => 'getCommentById',
                'content' => [
                    'comment' => $comment
                ]
            ]
        ]);
    }

    #[Route('/api/comment/list', name: 'get_comment_list', methods: ['GET'])]
    public function getCommentList(): JsonResponse
    {
        $localComment = $this->commentRepository->findAll();
        $comment = $this->autoMapper->mapMultiple($localComment, CommentListOut::class);

        return $this->json([
            'status' => true,
            'message' => [
                'class' => 'CommentController', 
                'method' => 'getCommentList',
                'content' => [
                    'comment' => $comment
                ]
            ]
        ]);
    }

    #[Route('/api/comment/{id}', name: 'delete_comment', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteComment($id): JsonResponse
    {
        $localComment = $this->commentRepository->find($id);
        $this->entityManager->remove($localComment);
        $this->entityManager->flush();
        
        return $this->json([
            'status' => true,
            'message' => [
                'class' => 'CommentController',
                'method' => 'deleteComment',
                'content' => [
                    'Delete_Row' => $id
                ]
            ]
        ]);
    }
}
