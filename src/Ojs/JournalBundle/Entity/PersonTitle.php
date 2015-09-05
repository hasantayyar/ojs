<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;
use Doctrine\Common\Collections\ArrayCollection;
use Ojs\CoreBundle\Entity\GenericEntityTrait;

/**
 * PersonTitle.
 *
 * @GRID\Source(columns="id, title")
 */
class PersonTitle extends AbstractTranslatable
{
    use GenericEntityTrait;

    /**
     * @var int
     * @Grid\Column(title="ID")
     */
    protected $id;

    /**
     * @Prezent\Translations(targetEntity="Ojs\JournalBundle\Entity\PersonTitleTranslation")
     */
    protected $translations;

    /**
     * @var string
     * @Grid\Column(title="Title")
     */
    private $title;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Translation helper method.
     *
     * @param null $locale
     *
     * @return mixed|null|\Ojs\JournalBundle\Entity\PersonTitleTranslation
     */
    public function translate($locale = null)
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }
        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }
        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }
        $defaultTranslation = $this->translations->get($this->getDefaultLocale());
        if (!$translation = $this->translations->get($locale)) {
            $translation = new PersonTitleTranslation();
            if (!is_null($defaultTranslation)) {
                $translation->setTitle($defaultTranslation->getTitle());
            }
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }
        $this->currentTranslation = $translation;

        return $translation;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return PersonTitle
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (!is_string($this->getTitle())) {
            return $this->translations->first()->getName();
        } else {
            return $this->getTitle();
        }
    }
}
