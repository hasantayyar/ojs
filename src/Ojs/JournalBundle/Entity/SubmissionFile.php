<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * SubmissionFile.
 *
 * @GRID\Source(columns="id,label,locale,visible")
 */
class SubmissionFile
{
    /**
     * @var int
     * @GRID\Column(title="ID")
     */
    private $id;

    /**
     * @var string
     * @GRID\Column(title="submission_file.label",safe = false)
     */
    private $label;

    /**
     * @var string
     */
    private $detail;

    /**
     * @var int
     */
    private $journalId;

    /**
     * @var int
     */
    private $articleId;

    /**
     * @var bool
     * @GRID\Column(title="submission_file.visible")
     */
    private $visible;

    /**
     * @var bool
     * @GRID\Column(title="submission_file.required")
     */
    private $required;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var Journal
     */
    private $journal;

    /**
     * @var Journal
     */
    private $article;

    /**
     * @var string
     * @GRID\Column(title="Locale")
     */
    private $locale;

    /**
     * @var string
     * @GRID\Column(title="file")
     */
    private $file;

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
     * set id.
     *
     * @param null $id
     *
     * @return $this
     */
    public function setId($id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return SubmissionFile
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get detail.
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set detail.
     *
     * @param string $detail
     *
     * @return SubmissionFile
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

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
     * @return SubmissionFile
     */
    public function setJournalId($journalId)
    {
        $this->journalId = $journalId;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return SubmissionFile
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get required.
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set required.
     *
     * @param bool $required
     *
     * @return SubmissionFile
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set deletedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return SubmissionFile
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

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
     * @return SubmissionFile
     */
    public function setJournal(Journal $journal = null)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return SubmissionFile
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return int
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @param int $articleId
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     *
     * @return $this
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }
}
