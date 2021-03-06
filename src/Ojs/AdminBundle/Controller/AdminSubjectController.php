<?php

namespace Ojs\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Ojs\AdminBundle\Form\Type\SubjectType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Subject;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;

/**
 * Subject controller.
 *
 */
class AdminSubjectController extends Controller
{

    /**
     * Lists all Subject entities.
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('VIEW', new Subject())) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }

        $source = new Entity("OjsJournalBundle:Subject");
        $source->manipulateRow(
            function (Row $row) use ($request) {
                /* @var Subject $entity */
                $entity = $row->getEntity();
                $entity->setDefaultLocale($request->getDefaultLocale());

                if (!is_null($entity)) {
                    $row->setField('subject', $entity->getSubject());
                    $row->setField('description', $entity->getDescription());
                }

                return $row;
            }
        );

        $grid = $this->get('grid')->setSource($source);

        $gridAction = $this->get('grid_action');
        $actionColumn = new ActionsColumn("actions", 'actions');
        $rowAction[] = $gridAction->showAction('ojs_admin_subject_show', 'id');
        $rowAction[] = $gridAction->editAction('ojs_admin_subject_edit', 'id');
        $rowAction[] = $gridAction->deleteAction('ojs_admin_subject_delete', 'id');
        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);

        /** @var ArrayCollection|Subject[] $all */
        $all = $this
            ->getDoctrine()
            ->getRepository('OjsJournalBundle:Subject')
            ->findAll();

        $data = [
            'grid' => $grid,
            'tree' => $this->createTreeView($all)
        ];

        return $grid->getGridResponse('OjsAdminBundle:AdminSubject:index.html.twig', $data);
    }

    /**
     * @param ArrayCollection $subjects
     * @param int|null $parentId
     * @return string
     */
    private function createTreeView($subjects, $parentId = null)
    {
        $tree = '<ul>%s</ul>';
        $item = '<li>%s</li>';
        $link = '<a href="%s">%s</a>';
        $items = "";

        /**
         * @var Subject $subject
         * @var ArrayCollection $children
         */
        foreach ($subjects as $subject) {
            if ($subject->getParent() == null || $subject->getParent()->getId() == $parentId) {
                $path = $this->get('router')->generate('ojs_admin_subject_show', ['id' => $subject->getId()]);
                $content = sprintf($link, $path, $subject->getSubject());
                $children = $subject->getChildren();

                if ($children->count() > 0) {
                    $content = $content.$this->createTreeView($children, $subject->getId());
                }

                $items = $items.sprintf($item, $content);
            }
        }

        return sprintf($tree, $items);
    }

    /**
     * Creates a new Subject entity.
     *
     * @param  Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('CREATE', new Subject())) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }
        $entity = new Subject();
        $entity->setCurrentLocale($request->getDefaultLocale());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute('ojs_admin_subject_show', ['id' => $entity->getId()]);
        }

        return $this->render(
            'OjsAdminBundle:AdminSubject:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a Subject entity.
     *
     * @param Subject $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Subject $entity)
    {
        $form = $this->createForm(
            new SubjectType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_subject_create'),
                'method' => 'POST',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Subject entity.
     *
     */
    public function newAction()
    {
        if (!$this->isGranted('CREATE', new Subject())) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }
        $entity = new Subject();
        $form = $this->createCreateForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminSubject:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Subject entity.
     *
     * @param Request $request
     * @param Subject $entity
     * @return Response
     */
    public function showAction(Request $request, Subject $entity)
    {
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }

        $entity->setDefaultLocale($request->getDefaultLocale());
        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_admin_subject'.$entity->getId());

        return $this->render(
            'OjsAdminBundle:AdminSubject:show.html.twig',
            ['entity' => $entity, 'token' => $token]
        );
    }

    /**
     * Displays a form to edit an existing Subject entity.
     *
     * @param  Subject $entity
     * @return Response
     */
    public function editAction(Subject $entity)
    {
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }
        $editForm = $this->createEditForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminSubject:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a Subject entity.
     *
     * @param Subject $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Subject $entity)
    {
        $form = $this->createForm(
            new SubjectType($entity->getId()),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_subject_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Subject entity.
     *
     * @param  Request $request
     * @param  Subject $entity
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, Subject $entity)
    {
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_admin_subject_edit', ['id' => $entity->getId()]);
        }

        return $this->render(
            'OjsAdminBundle:AdminSubject:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param  Request $request
     * @param  Subject $entity
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Subject $entity)
    {
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('DELETE', $entity)) {
            throw new AccessDeniedException("You are not authorized for this page!");
        }
        $em = $this->getDoctrine()->getManager();

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_admin_subject'.$entity->getId());
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException("Token Not Found!");
        }

        $em->remove($entity);
        $em->flush();
        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_admin_subject_index');
    }
}
