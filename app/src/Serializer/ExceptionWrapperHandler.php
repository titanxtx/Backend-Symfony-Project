<?php

namespace App\Serializer;

//use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use FOS\RestBundle\Serializer\Normalizer\ExceptionNormalizer;

class ExceptionWrapperHandler extends ExceptionNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = [];

        if (isset($context['template_data']['status_code'])) {
            $data['my_code'] = $statusCode = $context['template_data']['status_code'];
        }

        $data['my_message'] = $this->getExceptionMessage($object, isset($statusCode) ? $statusCode : null);
        $data['my_message']="THIs is a true test of my patience";
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Exception;
    }
}

?>