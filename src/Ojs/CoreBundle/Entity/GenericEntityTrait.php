<?php

namespace Ojs\CoreBundle\Entity;

/**
 * Class GenericEntityInterface.
 */
trait GenericEntityTrait
{
    use BlameableTrait;
    use SoftDeletableTrait;
    use TimestampableTrait;
    use TranslateableTrait;
    use TagsTrait;
    use DisplayTrait;
}
