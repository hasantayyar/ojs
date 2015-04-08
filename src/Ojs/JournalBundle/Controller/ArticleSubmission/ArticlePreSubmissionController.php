<?php

namespace Ojs\JournalBundle\Controller\ArticleSubmission;

use Ojs\JournalBundle\Document\ArticleSubmissionProgress;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ArticlePreSubmissionController
 * @package Ojs\JournalBundle\Controller\ArticleSubmission
 */
class ArticlePreSubmissionController extends Controller {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        $data = [];
        $journal = $this->get("ojs.journal_service")->getSelectedJournal();
        $data['journal'] = $journal;
        return $this->render('OjsJournalBundle:ArticleSubmission:preSubmission.html.twig', $data);
    }

    /**
     * @param Request $r
     * @param $locale
     * @return JsonResponse
     */
    public function saveAction(Request $r, $locale)
    {
        $submissionId = $r->get('submissionId', null);
        // save submission data to mongodb for resume action
        $dm = $this->get('doctrine_mongodb')->getManager();

        if (null === $submissionId) {
            $articleSubmission = new ArticleSubmissionProgress();
        } else {
            $articleSubmission = $dm->getRepository('OjsJournalBundle:ArticleSubmissionProgress')->find($submissionId);
        }
        $articleSubmission->setUserId($this->getUser()->getId());
        $articleSubmission->setStartedDate(new \DateTime());
        $articleSubmission->setLastResumeDate(new \DateTime());
        $articleSubmission->setLicences(json_encode($r->get('licence')));
        $articleSubmission->setCompetingOfInterest($r->get('competingOfInterest'));
        $dm->persist($articleSubmission);
        $dm->flush();


        return new JsonResponse([
            'submissionId' => $articleSubmission->getId(),
            'locale' => $locale]);
    }

}