<?php

namespace App;

use App\Entity\Article;
use App\Model\Out\Article\ArticleOut;
use App\Model\Out\Article\ArticleListOut;
use App\Model\In\Article\ArticleCreateIn;
use App\Model\In\Article\ArticleUpdateIn;

use App\Entity\Category;
use App\Model\Out\Category\CategoryOut;
use App\Model\Out\Category\CategoryListOut;
use App\Model\In\Category\CategoryCreateIn;
use App\Model\In\Category\CategoryUpdateIn;

use App\Entity\Comment;
use App\Model\Out\Comment\CommentOut;
use App\Model\Out\Comment\CommentListOut;
use App\Model\In\Comment\CommentCreateIn;
use App\Model\In\Comment\CommentUpdateIn;

use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;

class AutoMapperConfig implements AutoMapperConfiguratorInterface {

    private $entityManager;

    private $autoMapper;

    public function __construct(AutoMapperInterface $autoMapper, EntityManagerInterface $entityManager)
    {
        $this->autoMapper = $autoMapper;
        $this->entityManager = $entityManager;
    }

    public function configure(AutoMapperConfigInterface $config): void
    {
        $this->configureArticle($config);
        $this->configureCategory($config);
        $this->configureComment($config);
    }

    public function configureArticle(AutoMapperConfigInterface $config): void
    {            
        // ArticleCreateIn model -> Article entity
        $config->registerMapping(ArticleCreateIn::class, Article::class);

        // ArticleUpdateIn model -> Article entity
        $config->registerMapping(ArticleUpdateIn::class, Article::class);

        // Article entity -> ArticleOut model
        $config->registerMapping(Article::class, ArticleOut::class);

        // Article entity -> ArticleListOut model
        $config->registerMapping(Article::class, ArticleListOut::class);
    }

    public function configureCategory(AutoMapperConfigInterface $config): void
    {            
        // CategoryCreateIn model -> Category entity
        $config->registerMapping(CategoryCreateIn::class, Category::class);

        // CategoryUpdateIn model -> Category entity
        $config->registerMapping(CategoryUpdateIn::class, Category::class);

        // Category entity -> CategoryOut model
        $config->registerMapping(Category::class, CategoryOut::class);

        // Category entity -> CategoryListOut model
        $config->registerMapping(Category::class, CategoryListOut::class);
    }

    public function configureComment(AutoMapperConfigInterface $config): void
    {            
        // CommentCreateIn model -> Comment entity
        $config->registerMapping(CommentCreateIn::class, Comment::class);

        // CommentUpdateIn model -> Comment entity
        $config->registerMapping(CommentUpdateIn::class, Comment::class);

        // Comment entity -> CommentOut model
        $config->registerMapping(Comment::class, CommentOut::class);

        // Comment entity -> CommentListOut model
        $config->registerMapping(Comment::class, CommentListOut::class);
    }
}
