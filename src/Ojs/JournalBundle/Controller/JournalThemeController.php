<?php

namespace Ojs\JournalBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\Query;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\JournalTheme;
use Ojs\JournalBundle\Form\Type\JournalThemeType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * JournalTheme controller.
 *
 */
class JournalThemeController extends Controller
{

    /**
     * Lists all JournalTheme entities.
     *
     */
    public function indexAction()
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('VIEW', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        $source = new Entity('OjsJournalBundle:JournalTheme');

        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');

        $actionColumn = new ActionsColumn("actions", 'actions');

        $rowAction[] = $gridAction->showAction('ojs_journal_theme_show', ['id', 'journalId' => $journal->getId()]);
        $rowAction[] = $gridAction->editAction('ojs_journal_theme_edit', ['id', 'journalId' => $journal->getId()]);
        $rowAction[] = $gridAction->deleteAction('ojs_journal_theme_delete', ['id', 'journalId' => $journal->getId()]);

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        $data = [];
        $data['grid'] = $grid;

        return $grid->getGridResponse('OjsJournalBundle:JournalTheme:index.html.twig', $data);
    }

    /**
     * Creates a new JournalTheme entity.
     *
     * @param  Request                   $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('CREATE', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        $entity = new JournalTheme();
        $entity->setJournal($journal);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute('ojs_journal_theme_show', ['id' => $entity->getId(), 'journalId' => $journal->getId()]);
        }

        return $this->render(
            'OjsJournalBundle:JournalTheme:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a JournalTheme entity.
     *
     * @param JournalTheme $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(JournalTheme $entity)
    {
        $form = $this->createForm(
            new JournalThemeType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_journal_theme_create', ['journalId' => $entity->getJournal()->getId()]),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new JournalTheme entity.
     *
     */
    public function newAction()
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('CREATE', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        $entity = new JournalTheme();
        $entity->setJournal($journal);
        $form = $this->createCreateForm($entity);

        return $this->render(
            'OjsJournalBundle:JournalTheme:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a JournalTheme entity.
     *
     * @param  integer  $id
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $journal = $this->get('ojs.journal_service')->getSelectedJournal();

        if (!$this->isGranted('VIEW', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        /** @var JournalTheme $entity */
        $entity = $em->getRepository('OjsJournalBundle:JournalTheme')->find($id);
        $this->throw404IfNotFound($entity);

        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_journal_theme'.$entity->getId());

        return $this->render(
            'OjsJournalBundle:JournalTheme:show.html.twig',
            array(
                'entity' => $entity,
                'token'  => $token,
            )
        );
    }

    /**
     * Displays a form to edit an existing JournalTheme entity.
     *
     * @param  integer  $id
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $journal = $this->get('ojs.journal_service')->getSelectedJournal();

        if (!$this->isGranted('EDIT', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        /** @var JournalTheme $entity */
        $entity = $em->getRepository('OjsJournalBundle:JournalTheme')->find($id);
        $this->throw404IfNotFound($entity);
        $editForm = $this->createEditForm($entity);

        return $this->render(
            'OjsJournalBundle:JournalTheme:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a JournalTheme entity.
     *
     * @param JournalTheme $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(JournalTheme $entity)
    {
        $form = $this->createForm(
            new JournalThemeType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_journal_theme_update', array('id' => $entity->getId(), 'journalId' => $entity->getJournal()->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing JournalTheme entity.
     *
     * @param  Request                   $request
     * @param  integer                   $id
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $journal = $this->get('ojs.journal_service')->getSelectedJournal();

        if (!$this->isGranted('EDIT', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        /** @var JournalTheme $entity */
        $entity = $em->getRepository('OjsJournalBundle:JournalTheme')->find($id);
        $this->throw404IfNotFound($entity);

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_journal_theme_edit', ['id' => $entity->getId(), 'journalId' => $journal->getId()]);
        }

        return $this->render(
            'OjsJournalBundle:JournalTheme:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param  Request                                      $request
     * @param  integer                                      $id
     * @return RedirectResponse
     * @throws TokenNotFoundException|AccessDeniedException
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $journal = $this->get('ojs.journal_service')->getSelectedJournal();

        if (!$this->isGranted('DELETE', $journal, 'theme')) {
            throw new AccessDeniedException("You are not authorized for view this page");
        }
        /** @var JournalTheme $entity */
        $entity = $em->getRepository('OjsJournalBundle:JournalTheme')->find($id);
        $this->throw404IfNotFound($entity);

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_journal_theme'.$entity->getId());
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException("Token Not Found!");
        }
        $em->remove($entity);
        $em->flush();
        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_journal_theme_index', ['journalId' => $journal->getId()]);
    }
}
