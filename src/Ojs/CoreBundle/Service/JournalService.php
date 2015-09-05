<?php

namespace Ojs\CoreBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Ojs\JournalBundle\Entity\Journal;
use Ojs\JournalBundle\Entity\JournalRepository;
use Ojs\JournalBundle\Entity\JournalUser;
use Ojs\JournalBundle\Entity\Publisher;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Common methods for journal.
 */
class JournalService
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /** @var RequestStack */
    private $requestStack;

    /** @var  string */
    private $defaultPublisherSlug;

    /**
     * @param EntityManager         $em
     * @param Session               $session
     * @param Router                $router
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack          $requestStack
     * @param $defaultPublisherSlug
     */
    public function __construct(
        EntityManager $em,
        Session $session,
        Router $router,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        $defaultPublisherSlug
    ) {
        $this->session = $session;
        $this->em = $em;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->defaultPublisherSlug = $defaultPublisherSlug;
    }

    /**
     * @param Journal $journal
     *
     * @return bool|Journal
     */
    public function setSelectedJournal(Journal $journal = null)
    {
        if ($journal) {
            $this->session->set('selectedJournalId', $journal->getId());

            return $journal;
        }
        $token = $this->tokenStorage->getToken();
        if ($token instanceof AnonymousToken) {
            return false;
        }
        $user = $token->getUser();
        // set first journal if found
        /** @var JournalRepository $journalRepo */
        $journalRepo = $this->em->getRepository('OjsJournalBundle:Journal');
        $journal = $journalRepo->findOneByUser($user);
        if (!$journal instanceof Journal) {
            return false;
        }
        $this->session->set('selectedJournalId', $journal->getId());

        return $journal;
    }

    /**
     * @return bool|Journal
     */
    public function getJournalLocales()
    {
        $journal = $this->getSelectedJournal();
        $submissionLangObjects = $journal->getLanguages();
        $locales = [];
        foreach ($submissionLangObjects as $submissionLangObject) {
            $locales[] = $submissionLangObject->getCode();
        }

        return $locales;
    }

    /**
     * @return Journal
     */
    public function getSelectedJournal()
    {
        $journalId = $this->requestStack->getCurrentRequest()->attributes->get('journalId');

        if (!$journalId) {
            return false;
        }

        $selectedJournal = $this->em->getRepository('OjsJournalBundle:Journal')->find($journalId);

        if (!$selectedJournal) {
            return false;
        }

        return $selectedJournal;
    }

    /**
     * @param string  $checkRoles
     * @param Journal $journal
     *
     * @return bool
     *
     * @deprecated
     */
    public function hasJournalRole($checkRoles, Journal $journal = null)
    {
        $journalRoles = $this->getSelectedJournalRoles($journal)->toArray();

        if (is_array($checkRoles)) {
            foreach ($checkRoles as $role) {
                if (in_array($role, $journalRoles, true)) {
                    return true;
                }
            }

            return false;
        }

        return in_array($checkRoles, $journalRoles, true);
    }

    /**
     * @param Journal $journal
     *
     * @return Collection
     */
    public function getSelectedJournalRoles(Journal $journal = null)
    {
        $journal = $journal ? $journal : $this->getSelectedJournal();
        $token = $this->tokenStorage->getToken();

        if ($token instanceof AnonymousToken || (!$journal instanceof Journal)) {
            return array();
        }

        /* @var JournalUser $journalUser */
        $user = $token->getUser();
        $journalUserRepo = $this->em->getRepository('OjsJournalBundle:JournalUser');
        $journalUser = $journalUserRepo->findOneBy(['journal' => $journal, 'user' => $user]);

        if (!$journalUser) {
            return new ArrayCollection();
        }

        return $journalUser->getRoles();
    }

    /**
     * get default publisher record.
     *
     * @return Publisher
     */
    public function getDefaultPublisher()
    {
        $publisher = $this->defaultPublisherSlug ? $this->em->getRepository('OjsJournalBundle:Publisher')
            ->findOneBy(array('slug' => $this->defaultPublisherSlug)) : null;

        return $publisher;
    }

    /**
     * @param Journal $journal
     *
     * @return string
     */
    public function generateUrl(Journal $journal)
    {
        $publisher = $journal->getPublisher();
        $publisherSlug = $publisher ? $publisher->getSlug() : $this->defaultPublisherSlug;

        return $this->router
            ->generate(
                'ojs_journal_index',
                array('slug' => $journal->getSlug(), 'publisher' => $publisherSlug),
                Router::ABSOLUTE_URL
            );
    }

    /**
     * @param int $journalId
     * @param int $page
     * @param int $limit
     *
     * @return mixed user list
     */
    public function getUsers($journalId, $page, $limit)
    {
        $users = $this->em->getRepository('OjsJournalBundle:JournalRole')
            ->createQueryBuilder('j')
            ->where('j.journalId = :id')
            ->setParameter('id', $journalId)
            //->orderBy('id', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($page)
            ->getQuery()
            ->getResult();

        return $users;
    }
}
