<?php

namespace Ojs\SiteBundle\Controller;

use Ojs\AdminBundle\Form\Type\JournalApplicationType;
use Ojs\AdminBundle\Form\Type\PublisherApplicationType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Journal;
use Ojs\JournalBundle\Entity\JournalContact;
use Ojs\JournalBundle\Entity\Publisher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Application controller.
 *
 */
class ApplicationController extends Controller
{
    /**
     * @param  Request $request
     * @return RedirectResponse|Response
     */
    public function journalAction(Request $request)
    {
        $allowanceSetting = $this
            ->getDoctrine()
            ->getRepository('OjsAdminBundle:SystemSetting')
            ->findOneBy(['name' => 'journal_application']);

        if ($allowanceSetting) {
            if (!$allowanceSetting->getValue()) {
                return $this->render(
                    'OjsSiteBundle:Site:not_available.html.twig',
                    [
                        'title' => 'title.journal_application',
                        'message' => 'message.application_not_available'
                    ]
                );
            }
        }

        $application = new Journal();
        $application->setCurrentLocale($request->getDefaultLocale());
        $form = $this->createForm(new JournalApplicationType(), $application);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $application->setStatus(0);

                /** @var JournalContact $contact */
                if($application->getJournalContacts()){
                    foreach ($application->getJournalContacts() as $contact) {
                        $contact->setJournal($application);
                    }
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($application);
                $em->flush();

                return $this->redirect($this->get('router')->generate('ojs_apply_journal_success'));
            }

            $session = $this->get('session');
            $session->getFlashBag()->add('error', $this->get('translator')
                ->trans('An error has occured. Please check the form and resubmit.'));
            $session->save();
        }

        return $this->render('OjsSiteBundle:Application:journal.html.twig', array('form' => $form->createView()));
    }

    public function publisherAction(Request $request)
    {
        $allowanceSetting = $this
            ->getDoctrine()
            ->getRepository('OjsAdminBundle:SystemSetting')
            ->findOneBy(['name' => 'publisher_application']);

        if ($allowanceSetting) {
            if (!$allowanceSetting->getValue()) {
                return $this->render(
                    'OjsSiteBundle:Site:not_available.html.twig',
                    [
                        'title' => 'title.journal_application',
                        'message' => 'message.application_not_available'
                    ]
                );
            }
        }

        $em = $this->getDoctrine()->getManager();
        $application = new Publisher();

        $form = $this->createForm(new PublisherApplicationType(), $application);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($application);
                $em->flush();

                return $this->redirect($this->get('router')->generate('ojs_apply_institute_success'));
            }

            $session = $this->get('session');
            $session->getFlashBag()->add('error',$this->get('translator')
                ->trans('An error has occured. Please check form and resubmit.'));
        }

        return $this->render('OjsSiteBundle:Application:publisher.html.twig', array('form' => $form->createView()));
    }

    public function instituteSuccessAction()
    {
        return $this->render('OjsSiteBundle:Application:publisher_success.html.twig');
    }

    public function journalSuccessAction()
    {
        return $this->render('OjsSiteBundle:Application:journal_success.html.twig');
    }
}
