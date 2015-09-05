<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ojs\CoreBundle\Entity\GenericEntityTrait;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * JournalSection.
 *
 * @GRID\Source(columns="id,title,allowIndex,hideTitle,journal")
 */
class JournalSection extends AbstractTranslatable
{
    use GenericEntityTrait;

    /**
     * @var int
     * @GRID\Column(title="ID")
     */
    protected $id;
    /**
     * @Prezent\Translations(targetEntity="Ojs\JournalBundle\Entity\JournalSectionTranslation")
     */
    protected $translations;
    /**
     * @var string
     * @GRID\Column(title="journalsection.title")
     */
    private $title;
    /**
     * @var bool
     * @GRID\Column(title="journalsection.allow_index")
     */
    private $allowIndex = true;
    /**
     * @var bool
     * @GRID\Column(title="journalsection.hide_title")
     */
    private $hideTitle = false;
    /**
     * @var int
     */
    private $journalId;
    /**
     * @var Collection|Article[]
     */
    private $articles;
    /**
     * @var Journal
     * @GRID\Column(title="journal")
     */
    private $journal;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
     * Add articles.
     *
     * @param Article $article
     *
     * @return $this
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove articles.
     *
     * @param Article $article
     */
    public function removeArticle(Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles.
     *
     * @return Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Get allowIndex.
     *
     * @return bool
     */
    public function getAllowIndex()
    {
        return $this->allowIndex;
    }

    /**
     * Set allowIndex.
     *
     * @param bool $allowIndex
     *
     * @return JournalSection
     */
    public function setAllowIndex($allowIndex)
    {
        $this->allowIndex = $allowIndex;

        return $this;
    }

    /**
     * Get hideTitle.
     *
     * @return bool
     */
    public function getHideTitle()
    {
        return $this->hideTitle;
    }

    /**
     * Set hideTitle.
     *
     * @param bool $hideTitle
     *
     * @return JournalSection
     */
    public function setHideTitle($hideTitle)
    {
        $this->hideTitle = $hideTitle;

        return $this;
    }

    /**
     * Get journalId.
     *
     * @return int
     */
    public function getJournalId()
    {
        return $this->journalId;
    }

    /**
     * Set journalId.
     *
     * @param int $journalId
     *
     * @return JournalSection
     */
    public function setJournalId($journalId)
    {
        $this->journalId = $journalId;

        return $this;
    }

    /**
     * Get journal.
     *
     * @return Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Set journal.
     *
     * @param Journal $journal
     *
     * @return JournalSection
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
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
     * Set title.
     *
     * @param string $title
     *
     * @return JournalSection
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * Translation helper method.
     *
     * @param null $locale
     *
     * @return mixed|null|\Ojs\JournalBundle\Entity\JournalSectionTranslation
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
            $translation = new JournalSectionTranslation();
            if (!is_null($defaultTranslation)) {
                $translation->setTitle($defaultTranslation->getTitle());
            }
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }
        $this->currentTranslation = $translation;

        return $translation;
    }
}
