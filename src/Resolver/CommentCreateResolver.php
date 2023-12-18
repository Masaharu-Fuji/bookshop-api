<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\Comment;
use App\Repository\CategoryRepository;
use App\Repository\WriterRepository;

class CommentCreateResolver implements MutationResolverInterface
{
    private $writerRepository;
    private $categoryRepository;

    public function __construct(
        WriterRepository $writerRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->writerRepository = $writerRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Comment|null $comment
     */
    public function __invoke( //interfaceより実装
        $comment,
        array $context,
    ): ?Comment {

        if (!$comment instanceof Comment) {
            return null;
        }

        //入力時にIRIがあるかどうか .nullの場合、keyも加えられない。
        if (!array_key_exists('writer', $context['args']['input'])) {
            $comment->setWriter(
                $this->getDefaultWriterQuery()
            );
        }

        if (!array_key_exists('category', $context['args']['input'])) {
            $comment->setCategory(
                $this->getDefaultCategoryQuery()
            );
        }
        return $comment;
    }

    private function getDefaultWriterQuery()
    {
        return $this->writerRepository->find(1);
    }

    private function getDefaultCategoryQuery()
    {
        return $this->categoryRepository->find(1);
    }
}
