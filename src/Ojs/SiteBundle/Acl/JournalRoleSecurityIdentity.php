<?php

namespace Ojs\SiteBundle\Acl;

use Ojs\JournalBundle\Entity\Journal;
use Ojs\UserBundle\Entity\Role;
use Ojs\UserBundle\Entity\UserJournalRole;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;

class JournalRoleSecurityIdentity implements SecurityIdentityInterface
{

    /**
     * @var string
     */
    protected $role;

    /**
     * @var integer
     */
    protected $journal;

    /**
     * @param $journal
     * @param $role
     */
    public function __construct($journal = null, $role = null)
    {
        if (empty($journal)) {
            throw new \InvalidArgumentException('$journal must not be empty.');
        }
        if (empty($role)) {
            throw new \InvalidArgumentException('$role must not be empty.');
        }
        if ($journal instanceof Journal) {
            $journal = $journal->getId();
        }
        if ($role instanceof Role) {
            $role = $role->getName();
        }
        $this->journal = $journal;
        $this->role = $role;
    }

    public static function fromUserJournalRole(UserJournalRole $userJournalRole)
    {
        return new self($userJournalRole->getJournal(), $userJournalRole->getRole());
    }

    /**
     * Returns the role name.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Returns the journal ID.
     *
     * @return string
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(SecurityIdentityInterface $sid)
    {
        if (!$sid instanceof JournalRoleSecurityIdentity) {
            return false;
        }

        return ($this->role === $sid->getRole() && $this->journal === $sid->getJournal());
    }

    /**
     * Returns a textual representation of this security identity.
     *
     * This is solely used for debugging purposes, not to make an equality decision.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('JournalRoleSecurityIdentity-(%s)-(%s)', $this->role, $this->journal);
    }
}