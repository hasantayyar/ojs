<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Ojs\CoreBundle\Entity\GenericEntityTrait;

/**
 * JournalTheme.
 *
 * @GRID\Source(columns="id,journal.title,title")
 */
class JournalTheme
{
    use GenericEntityTrait;

    /**
     * @var int
     * @GRID\Column(title="id")
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $css;

    /**
     * @var bool
     */
    private $isPublic;

    /**
     * @var Journal
     */
    private $journal;

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
     * @return JournalTheme
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @param string $css
     */
    public function setCss($css)
    {
        $this->css = $css;
    }

    /**
     * @return bool
     */
    public function isIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }
}
