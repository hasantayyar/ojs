<?php

namespace Ojs\AnalyticsBundle\Listener;

use Doctrine\ORM\EntityManager;
use Ojs\AnalyticsBundle\Entity\ArticleFileStatistic;
use Ojs\AnalyticsBundle\Entity\ArticleStatistic;
use Ojs\AnalyticsBundle\Entity\IssueFileStatistic;
use Ojs\AnalyticsBundle\Entity\IssueStatistic;
use Ojs\AnalyticsBundle\Entity\JournalStatistic;
use Ojs\JournalBundle\Entity\Article;
use Ojs\SiteBundle\Event\DownloadArticleFileEvent;
use Ojs\SiteBundle\Event\DownloadIssueFileEvent;
use Ojs\SiteBundle\Event\SiteEvents;
use Ojs\SiteBundle\Event\ViewArticleEvent;
use Ojs\SiteBundle\Event\ViewIssueEvent;
use Ojs\SiteBundle\Event\ViewJournalEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AnalyticsSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * AnalyticsSubscriber constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::VIEW_ISSUE => 'onIssueView',
            SiteEvents::VIEW_JOURNAL => 'onJournalView',
            SiteEvents::VIEW_ARTICLE => 'onArticleView',
            SiteEvents::DOWNLOAD_ISSUE_FILE => 'onIssueFileDownload',
            SiteEvents::DOWNLOAD_ARTICLE_FILE => 'onArticleFileDownload',
        );
    }

    public function onArticleView(ViewArticleEvent $event)
    {
        $stat = $this->em
            ->getRepository('OjsAnalyticsBundle:ArticleStatistic')
            ->findOneBy(['date' => new \DateTime(), 'article' => $event->getArticle()]);

        if (!$stat) {
            $stat = new ArticleStatistic();
            $stat->setDate(new \DateTime());
            $stat->setArticle($event->getArticle());
            $stat->setView(1);
        } else {
            $stat->setView($stat->getView() + 1);
        }

        $this->em->persist($stat);
        $this->em->flush($stat);
    }

    public function onIssueView(ViewIssueEvent $event)
    {
        $stat = $this->em
            ->getRepository('OjsAnalyticsBundle:IssueStatistic')
            ->findOneBy(['date' => new \DateTime(), 'issue' => $event->getIssue()]);

        if (!$stat) {
            $stat = new IssueStatistic();
            $stat->setDate(new \DateTime());
            $stat->setIssue($event->getIssue());
            $stat->setView(1);
        } else {
            $stat->setView($stat->getView() + 1);
        }

        $this->em->persist($stat);
        $this->em->flush($stat);
    }

    public function onJournalView(ViewJournalEvent $event)
    {
        $stat = $this->em
            ->getRepository('OjsAnalyticsBundle:JournalStatistic')
            ->findOneBy(['date' => new \DateTime(), 'journal' => $event->getJournal()]);

        if (!$stat) {
            $stat = new JournalStatistic();
            $stat->setDate(new \DateTime());
            $stat->setJournal($event->getJournal());
            $stat->setView(1);
        } else {
            $stat->setView($stat->getView() + 1);
        }

        $this->em->persist($stat);
        $this->em->flush($stat);
    }

    public function onArticleFileDownload(DownloadArticleFileEvent $event)
    {
        $stat = $this->em
            ->getRepository('OjsAnalyticsBundle:ArticleFileStatistic')
            ->findOneBy(['date' => new \DateTime(), 'articleFile' => $event->getArticleFile()]);

        if (!$stat) {
            $stat = new ArticleFileStatistic();
            $stat->setDate(new \DateTime());
            $stat->setArticleFile($event->getArticleFile());
            $stat->setDownload(1);
        } else {
            $stat->setDownload($stat->getDownload() + 1);
        }

        $this->em->persist($stat);
        $this->em->flush($stat);
    }

    public function onIssueFileDownload(DownloadIssueFileEvent $event)
    {
        $stat = $this->em
            ->getRepository('OjsAnalyticsBundle:IssueFileStatistic')
            ->findOneBy(['date' => new \DateTime(), 'issueFile' => $event->getIssueFile()]);

        if (!$stat) {
            $stat = new IssueFileStatistic();
            $stat->setDate(new \DateTime());
            $stat->setIssueFile($event->getIssueFile());
            $stat->setDownload(1);
        } else {
            $stat->setDownload($stat->getDownload() + 1);
        }

        $this->em->persist($stat);
        $this->em->flush($stat);
    }
}
