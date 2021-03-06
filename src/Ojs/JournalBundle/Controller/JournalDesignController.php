<?php

namespace Ojs\JournalBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\Query;
use Elastica\Exception\NotFoundException;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Journal;
use Ojs\JournalBundle\Entity\JournalDesign;
use Ojs\JournalBundle\Form\Type\JournalDesignType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;

/**
 * JournalDesign controller.
 *
 */
class JournalDesignController extends Controller
{

    /**
     * Lists all JournalDesign entities.
     *
     */
    public function indexAction()
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('VIEW', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for view this journal's designs!");
        }
        $source = new Entity('OjsJournalBundle:JournalDesign');

        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');

        $actionColumn = new ActionsColumn("actions", 'actions');

        $rowAction[] = $gridAction->showAction('ojs_journal_design_show', ['id', 'journalId' => $journal->getId()]);
        $rowAction[] = $gridAction->editAction('ojs_journal_design_edit', ['id', 'journalId' => $journal->getId()]);
        $rowAction[] = $gridAction->deleteAction('ojs_journal_design_delete', ['id', 'journalId' => $journal->getId()]);

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        $data = [];
        $data['grid'] = $grid;

        return $grid->getGridResponse('OjsJournalBundle:JournalDesign:index.html.twig', $data);
    }

    /**
     * Creates a new JournalDesign entity.
     *
     * @param  Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('CREATE', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for create a this journal's design!");
        }
        $entity = new JournalDesign();
        $form = $this->createCreateForm($entity, $journal);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setContent(
                $this->prepareDesignContent($entity->getEditableContent())
            );
            $entity->setJournal($journal);
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute(
                'ojs_journal_design_show',
                ['id' => $entity->getId(), 'journalId' => $journal->getId()]
            );
        }

        return $this->render(
            'OjsJournalBundle:JournalDesign:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param JournalDesign $entity
     * @param Journal $journal
     * @return Form
     */
    private function createCreateForm(JournalDesign $entity, Journal $journal)
    {
        $form = $this->createForm(
            new JournalDesignType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_journal_design_create', ['journalId' => $journal->getId()]),
                'method' => 'POST',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @param  String $editableContent
     * @return String
     */
    private function prepareDesignContent($editableContent)
    {
        $editableContent = preg_replace_callback(
            '/<span\s*class\s*=\s*"\s*design-hide-block[^"]*"[^>]*>.*<\s*\/\s*span\s*>.*<span\s*class\s*=\s*"\s*design-hide-endblock[^"]*"[^>]*>.*<\s*\/\s*span\s*>/Us',
            function ($matches) {
                preg_match('/<!---.*--->/Us', $matches[0], $matched);

                return str_ireplace(['<!---', '--->'], '', $matched[0]);
            },
            $editableContent
        );
        $editableContent = preg_replace_callback(
            '/<span\s*class\s*=\s*"\s*design-hide-span[^"]*"[^>]*>.*<\s*\/\s*span\s*>/Us',
            function ($matches) {
                preg_match('/<!---.*--->/Us', $matches[0], $matched);

                return str_ireplace(['<!---', '--->'], '', $matched[0]);
            },
            $editableContent
        );
        $editableContent = preg_replace_callback(
            '/<span\s*class\s*=\s*"\s*design-inline[^"]*"[^>]*>.*<\s*\/\s*span\s*>/Us',
            function ($matches) {
                preg_match('/title\s*=\s*"\s*{.*}\s*"/Us', $matches[0], $matched);
                $matched[0] = preg_replace('/title\s*=\s*"/Us', '', $matched[0]);

                return str_replace('"', '', $matched[0]);
            },
            $editableContent
        );
        $editableContent = str_ireplace('<!--gm-editable-region-->', '', $editableContent);
        $editableContent = str_ireplace('<!--/gm-editable-region-->', '', $editableContent);

        return $editableContent;
    }

    /**
     * Displays a form to create a new JournalDesign entity.
     *
     * @return Response
     */
    public function newAction()
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('CREATE', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for create a this journal's design!");
        }
        $entity = new JournalDesign();
        $form = $this->createCreateForm($entity, $journal);

        return $this->render(
            'OjsJournalBundle:JournalDesign:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('VIEW', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for view this journal's design!");
        }
        /** @var JournalDesign $design */
        $design = $em->getRepository('OjsJournalBundle:JournalDesign')->find($id);
        $this->throw404IfNotFound($design);
        if ($design->getJournal()->getId() !== $journal->getId()) {
            throw new NotFoundException("Journal Design not found!");
        }
        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_journal_design'.$id);

        return $this->render(
            'OjsJournalBundle:JournalDesign:show.html.twig',
            array(
                'entity' => $design,
                'token' => $token,
            )
        );
    }

    /**
     *
     * @param  JournalDesign $journalDesign
     * @return Response
     */
    public function editAction(JournalDesign $journalDesign)
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('EDIT', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for edit this journal's design!");
        }
        $journalDesign->setEditableContent($this->prepareEditContent($journalDesign->getEditableContent()));
        $editForm = $this->createEditForm($journalDesign, $journal);

        return $this->render(
            'OjsJournalBundle:JournalDesign:edit.html.twig',
            array(
                'journal' => $journal,
                'entity' => $journalDesign,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param  String $editableContent
     * @return String
     */
    private function prepareEditContent($editableContent)
    {
        $editableContent = str_ireplace('<!--raw-->', '{% raw %}<!--raw-->', $editableContent);
        $editableContent = str_ireplace('<!--endraw-->', '{% endraw %}<!--endraw-->', $editableContent);

        $editableContent = preg_replace_callback(
            '/<span\s*class\s*=\s*"\s*design-inline[^"]*"[^>]*>.*<\s*\/\s*span\s*>/Us',
            function ($matches) {
                return preg_replace_callback(
                    '/{{.*}}/Us',
                    function ($matched) {
                        return '{{ "'.addcslashes($matched[0], '"').'" }}';
                    },
                    $matches[0]
                );
            },
            $editableContent
        );

        return $editableContent;
    }

    /**
     * @param JournalDesign $entity
     * @param Journal $journal
     * @return Form
     */
    private function createEditForm(JournalDesign $entity, Journal $journal)
    {
        $form = $this->createForm(
            new JournalDesignType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'ojs_journal_design_update',
                    [
                        'journalId' => $journal->getId(),
                        'id' => $entity->getId()
                    ]
                ),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     *
     * @param  Request $request
     * @param  JournalDesign $journalDesign
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, JournalDesign $journalDesign)
    {
        $em = $this->getDoctrine()->getManager();
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('EDIT', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for view this journal's sections!");
        }

        $editForm = $this->createEditForm($journalDesign, $journal);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $journalDesign->setContent(
                $this->prepareDesignContent($journalDesign->getEditableContent())
            );
            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute(
                'ojs_journal_design_edit',
                ['id' => $journalDesign->getId(), 'journalId' => $journal->getId()]
            );
        }

        return $this->render(
            'OjsJournalBundle:JournalDesign:edit.html.twig',
            array(
                'journal' => $journal,
                'entity' => $journalDesign,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param  Request $request
     * @param  integer $id
     * @return RedirectResponse
     * @throws TokenNotFoundException
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        if (!$this->isGranted('DELETE', $journal, 'design')) {
            throw new AccessDeniedException("You are not authorized for view this journal's sections!");
        }

        $requestedDesign = $em->getRepository('OjsJournalBundle:JournalDesign')->find($id);

        if ($id == $journal->getDesign()->getId()) {
            $this->errorFlashBag('journal.design.cannot_delete_active');
        } else {
            $csrf = $this->get('security.csrf.token_manager');
            $token = $csrf->getToken('ojs_journal_design'.$id);

            if ($token != $request->get('_token')) {
                throw new TokenNotFoundException("Token Not Found!");
            }

            $em->remove($requestedDesign);
            $em->flush();

            $this->successFlashBag('successful.remove');
        }

        return $this->redirectToRoute('ojs_journal_design_index', ['journalId' => $journal->getId()]);
    }
}
