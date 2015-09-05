<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Ojs\CoreBundle\Entity\GenericEntityTrait;

/**
 * PublisherTheme.
 *
 * @GRID\Source(columns="id,journal.title,title")
 */
class PublisherTheme
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
     * @var Publisher
     */
    private $publisher;

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
     * Get Publisher.
     *
     * @return Publisher
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Set Publisher.
     *
     * @param Publisher $publisher
     *
     * @return PublisherTheme
     */
    public function setPublisher(Publisher $publisher)
    {
        $this->publisher = $publisher;

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
