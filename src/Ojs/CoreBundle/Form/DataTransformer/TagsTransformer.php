<?php

namespace Ojs\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TagsTransformer.
 */
class TagsTransformer implements DataTransformerInterface
{
    private $delimiter = ',';
    public function transform($tagsData)
    {
        if (empty($tagsData)) {
            return;
        }
        $parts = explode($this->delimiter, $tagsData);

        return $parts;
    }

    public function reverseTransform($values = null)
    {
        if (empty($values)) {
            return;
        }

        return implode($this->delimiter, $values);
    }
}
