<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface as ResolverMutationResolverInterface;
use App\Entity\Writer;

class WriterCreateResolver implements ResolverMutationResolverInterface
{
    /**
     * @param Writer|null $writer
     */
    public function __invoke($writer, array $context): ?Writer
    {
        if (!$writer instanceof Writer) {
            return null;
        }

        // $context['args'] にオペレーションに渡された引数が入っている
        //$writer->setNickname($context['args']['input']['nickname'] ?? 'unknown');

        return $writer;
    }
}
