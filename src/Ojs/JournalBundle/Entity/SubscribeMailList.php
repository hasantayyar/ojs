<?php

namespace Ojs\JournalBundle\Entity;

use Gedmo\Translatable\Translatable;
use Ojs\CoreBundle\Entity\GenericEntityTrait;

/**
 * SubscribeMailList.
 */
class SubscribeMailList implements Translatable
{
    use GenericEntityTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $journalId;

    /**
     * @var Journal
     */
    private $journal;

    /**
     * @var string
     */
    private $mail;

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
     * @return Issue
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

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
     * @return Issue
     */
    public function setJournalId($journalId)
    {
        $this->journalId = $journalId;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return $this
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return SubscribeMailList
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return SubscribeMailList
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
