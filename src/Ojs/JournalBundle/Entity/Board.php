<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Translatable\Translatable;
use Ojs\CoreBundle\Entity\GenericEntityTrait;

/**
 * Board.
 *
 * @GRID\Source(columns="id , journal.title, name, description")
 */
class Board implements Translatable
{
    use GenericEntityTrait;

    /**
     * @var int
     * @GRID\Column(title="id")
     */
    private $id;

    /**
     * @var int
     */
    private $journalId;

    /**
     * @var string
     * @GRID\Column(title="name")
     */
    private $name;

    /**
     * @var string
     * @GRID\Column(title="description")
     */
    private $description;

    /**
     * @var Journal
     */
    private $journal;

    /**
     * @var Collection|BoardMember[]
     */
    private $boardMembers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->boardMembers = new ArrayCollection();
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
     * @return Board
     */
    public function setJournalId($journalId)
    {
        $this->journalId = $journalId;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Board
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * @return Board
     */
    public function setJournal(Journal $journal = null)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Add boardMembers.
     *
     * @param BoardMember $boardMembers
     *
     * @return Board
     */
    public function addBoardMember(BoardMember $boardMembers)
    {
        $this->boardMembers[] = $boardMembers;

        return $this;
    }

    /**
     * Remove boardMembers.
     *
     * @param BoardMember $boardMembers
     */
    public function removeBoardMember(BoardMember $boardMembers)
    {
        $this->boardMembers->removeElement($boardMembers);
    }

    /**
     * Get boardMembers.
     *
     * @return Collection
     */
    public function getBoardMembers()
    {
        return $this->boardMembers;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Board
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
