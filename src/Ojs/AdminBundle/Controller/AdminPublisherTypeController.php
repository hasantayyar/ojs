<?php

namespace Ojs\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\AdminBundle\Form\Type\PublisherTypesType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\PublisherTypes;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * PublisherTypes controller.
 */
class AdminPublisherTypeController extends Controller
{
    /**
     * Lists all PublisherTypes entities.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('VIEW', new PublisherTypes())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $source = new Entity('OjsJournalBundle:PublisherTypes');
        $source->manipulateRow(
            function (Row $row) use ($request) {
                /**
                 * @var PublisherTypes
                 */
                $entity = $row->getEntity();
                $entity->setDefaultLocale($request->getDefaultLocale());
                if (!is_null($entity)) {
                    $row->setField('name', $entity->getName());
                    $row->setField('description', $entity->getDescription());
                }

                return $row;
            }
        );
        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');

        $actionColumn = new ActionsColumn('actions', 'actions');

        $rowAction[] = $gridAction->showAction('ojs_admin_publisher_type_show', 'id');
        $rowAction[] = $gridAction->editAction('ojs_admin_publisher_type_edit', 'id');
        $rowAction[] = $gridAction->deleteAction('ojs_admin_publisher_type_delete', 'id');

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        $data = [];
        $data['grid'] = $grid;

        return $grid->getGridResponse('OjsAdminBundle:AdminPublisherType:index.html.twig', $data);
    }

    /**
     * Creates a new PublisherTypes entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('CREATE', new PublisherTypes())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new PublisherTypes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute('ojs_admin_publisher_type_show', ['id' => $entity->getId()]);
        }

        return $this->render(
            'OjsAdminBundle:AdminPublisherType:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a PublisherTypes entity.
     *
     * @param PublisherTypes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PublisherTypes $entity)
    {
        $form = $this->createForm(
            new PublisherTypesType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_publisher_type_create'),
                'method' => 'POST',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PublisherTypes entity.
     */
    public function newAction()
    {
        if (!$this->isGranted('CREATE', new PublisherTypes())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new PublisherTypes();
        $form = $this->createCreateForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminPublisherType:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a PublisherTypes entity.
     *
     * @param Request        $request
     * @param PublisherTypes $entity
     *
     * @return Response
     */
    public function showAction(Request $request, PublisherTypes $entity)
    {
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        $entity->setDefaultLocale($request->getDefaultLocale());
        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_admin_publisher_type'.$entity->getId());

        return $this->render(
            'OjsAdminBundle:AdminPublisherType:show.html.twig',
            ['entity' => $entity, 'token' => $token]
        );
    }

    /**
     * Displays a form to edit an existing PublisherTypes entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PublisherTypes $entity */
        $entity = $em->getRepository('OjsJournalBundle:PublisherTypes')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminPublisherType:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a PublisherTypes entity.
     *
     * @param PublisherTypes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PublisherTypes $entity)
    {
        $form = $this->createForm(
            new PublisherTypesType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_publisher_type_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing PublisherTypes entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PublisherTypes $entity */
        $entity = $em->getRepository('OjsJournalBundle:PublisherTypes')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_admin_publisher_type_edit', ['id' => $id]);
        }

        return $this->render(
            'OjsAdminBundle:AdminPublisherType:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param Request        $request
     * @param PublisherTypes $entity
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws TokenNotFoundException
     */
    public function deleteAction(Request $request, PublisherTypes $entity)
    {
        if (!$this->isGranted('DELETE', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $this->throw404IfNotFound($entity);
        $em = $this->getDoctrine()->getManager();

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_admin_publisher_type'.$entity->getId());
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException('Token Not Found!');
        }
        $em->remove($entity);
        $em->flush();
        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_admin_publisher_type_index');
    }
}
