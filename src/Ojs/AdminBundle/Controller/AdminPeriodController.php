<?php

namespace Ojs\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\AdminBundle\Form\Type\PeriodType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Period;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * Period controller.
 */
class AdminPeriodController extends Controller
{
    /**
     * Lists all Period entities.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('VIEW', new Period())) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $source = new Entity('OjsJournalBundle:Period');
        $source->manipulateRow(
            function ($row) use ($request) {
                /**
                 * @var \APY\DataGridBundle\Grid\Row
                 * @var Period
                 */
                $entity = $row->getEntity();
                $entity->setDefaultLocale($request->getDefaultLocale());
                if (!is_null($entity)) {
                    $row->setField('period', $entity->getPeriod());
                }

                return $row;
            }
        );

        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');

        $actionColumn = new ActionsColumn('actions', 'actions');

        $rowAction[] = $gridAction->showAction('ojs_admin_period_show', 'id');
        $rowAction[] = $gridAction->editAction('ojs_admin_period_edit', 'id');
        $rowAction[] = $gridAction->deleteAction('ojs_admin_period_delete', 'id');

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        $data = [];
        $data['grid'] = $grid;

        return $grid->getGridResponse('OjsAdminBundle:AdminPeriod:index.html.twig', $data);
    }

    /**
     * Creates a new Period entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('CREATE', new Period())) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $entity = new Period();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute('ojs_admin_period_show', ['id' => $entity->getId()]);
        }

        return $this->render(
            'OjsAdminBundle:AdminPeriod:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a Period entity.
     *
     * @param Period $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Period $entity)
    {
        $form = $this->createForm(
            new PeriodType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_period_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Period entity.
     */
    public function newAction()
    {
        if (!$this->isGranted('CREATE', new Period())) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $entity = new Period();
        $form = $this->createCreateForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminPeriod:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Period entity.
     *
     * @param Period $entity
     *
     * @return Response
     */
    public function showAction(Period $entity)
    {
        if (!$this->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $this->throw404IfNotFound($entity);

        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_admin_period'.$entity->getId());

        return $this->render(
            'OjsAdminBundle:AdminPeriod:show.html.twig',
            array(
                'entity' => $entity,
                'token' => $token,
            )
        );
    }

    /**
     * Displays a form to edit an existing Period entity.
     *
     * @param Period $entity
     *
     * @return Response
     */
    public function editAction(Period $entity)
    {
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $this->throw404IfNotFound($entity);
        $editForm = $this->createEditForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminPeriod:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a Period entity.
     *
     * @param Period $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Period $entity)
    {
        $form = $this->createForm(
            new PeriodType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_period_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Period entity.
     *
     * @param Request $request
     * @param Period  $entity
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, Period $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $this->throw404IfNotFound($entity);

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_admin_period_edit', ['id' => $entity->getId()]);
        }

        return $this->render(
            'OjsAdminBundle:AdminPeriod:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Period  $entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Period $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$this->isGranted('DELETE', $entity)) {
            throw new AccessDeniedException('You are not authorized for view this page');
        }
        $this->throw404IfNotFound($entity);

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_admin_period'.$entity->getId());
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException('Token Not Found!');
        }
        $em->remove($entity);
        $em->flush();
        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_admin_period_index');
    }
}
