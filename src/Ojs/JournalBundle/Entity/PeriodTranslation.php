<?php

namespace Ojs\JournalBundle\Entity;

use Ojs\CoreBundle\Entity\DisplayTrait;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * JournalPeriod.
 */
class PeriodTranslation extends AbstractTranslation
{
    use DisplayTrait;

    /**
     * @Prezent\Translatable(targetEntity="Ojs\JournalBundle\Entity\Period")
     */
    protected $translatable;

    /**
     * @var int
     */
    private $period;

    /**
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param int $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }
}
