<?php

namespace Ojs\JournalBundle\Entity;

use Gedmo\Translatable\Translatable;
use Ojs\CoreBundle\Entity\GenericEntityTrait;
use Ojs\UserBundle\Entity\User;

/**
 * BoardMember.
 */
class BoardMember implements Translatable
{
    use GenericEntityTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $boardId;

    /**
     * @var int
     */
    private $seq;

    /**
     * @var Board
     */
    private $board;

    /**
     * @var User
     */
    private $user;

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
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return BoardMember
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get boardId.
     *
     * @return int
     */
    public function getBoardId()
    {
        return $this->boardId;
    }

    /**
     * Set boardId.
     *
     * @param int $boardId
     *
     * @return BoardMember
     */
    public function setBoardId($boardId)
    {
        $this->boardId = $boardId;

        return $this;
    }

    /**
     * Get seq.
     *
     * @return int
     */
    public function getSeq()
    {
        return $this->seq;
    }

    /**
     * Set seq.
     *
     * @param int $seq
     *
     * @return BoardMember
     */
    public function setSeq($seq)
    {
        $this->seq = $seq;

        return $this;
    }

    /**
     * Get board.
     *
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set board.
     *
     * @param Board $board
     *
     * @return BoardMember
     */
    public function setBoard(Board $board = null)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return BoardMember
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
