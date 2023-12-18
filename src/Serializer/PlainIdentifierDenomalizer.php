<?php

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Dummy;
use App\Entity\RelatedDummy;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class PlainIdentifierDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }
    /**
     * {@inheritdoc}
     */
    public function denormalize(
        $data,
        $class,
        $format = null,
        array $context = []
    ) {
        $data['relatedDummy'] = $this->iriConverter->getIriFromResource(
            resource: RelatedDummy::class,
            context: [
                'uri_variables' => [
                    'id' => $data['relatedDummy']
                ]
            ]
        );
        return $this->denormalizer->denormalize(
            $data,
            $class,
            $format,
            $context + [__CLASS__ => true]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(
        $data,
        $type,
        $format = null,
        array $context = []
    ): bool {
        return \in_array(
            $format,
            ['json', 'jsonld'],
            true
        )
            &&
            is_a(
                $type,
                Dummy::class,
                true
            )
            &&
            !empty($data['relatedDummy'])
            &&
            !isset($context[__CLASS__]);
    }
}
