<?php

namespace Ojs\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\AdminBundle\Form\Type\PublisherDesignType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\PublisherDesign;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * PublisherDesign controller.
 */
class AdminPublisherDesignController extends Controller
{
    /**
     * Lists all PublisherDesigns entities.
     */
    public function indexAction()
    {
        if (!$this->isGranted('VIEW', new PublisherDesign())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $source = new Entity('OjsJournalBundle:PublisherDesign');
        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');

        $actionColumn = new ActionsColumn('actions', 'actions');

        $rowAction[] = $gridAction->showAction('ojs_admin_publisher_design_show', 'id');
        $rowAction[] = $gridAction->editAction('ojs_admin_publisher_design_edit', 'id');
        $rowAction[] = $gridAction->deleteAction('ojs_admin_publisher_design_delete', 'id');

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        $data = [];
        $data['grid'] = $grid;

        return $grid->getGridResponse('OjsAdminBundle:AdminPublisherDesign:index.html.twig', $data);
    }

    /**
     * Creates a new PublisherDesign entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('CREATE', new PublisherDesign())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new PublisherDesign();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setContent(
                $this->prepareDesignContent($entity->getEditableContent())
            );
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute('ojs_admin_publisher_design_show', ['id' => $entity->getId()]);
        }

        return $this->render(
            'OjsAdminBundle:AdminPublisherDesign:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a PublisherTypes entity.
     *
     * @param PublisherDesign $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PublisherDesign $entity)
    {
        $form = $this->createForm(
            new PublisherDesignType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_publisher_design_create'),
                'method' => 'POST',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @param String $editableContent
     *
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
     * Displays a form to create a new PublisherDesign entity.
     */
    public function newAction()
    {
        if (!$this->isGranted('CREATE', new PublisherDesign())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new PublisherDesign();
        $form = $this->createCreateForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminPublisherDesign:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a PublisherDesign entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsJournalBundle:PublisherDesign')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_admin_publisher_Design'.$entity->getId());

        return $this->render(
            'OjsAdminBundle:AdminPublisherDesign:show.html.twig',
            ['entity' => $entity, 'token' => $token]
        );
    }

    /**
     * Displays a form to edit an existing PublisherDesign entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PublisherDesign $entity */
        $entity = $em->getRepository('OjsJournalBundle:PublisherDesign')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity->setEditableContent($this->prepareEditContent($entity->getEditableContent()));
        $editForm = $this->createEditForm($entity);

        return $this->render(
            'OjsAdminBundle:AdminPublisherDesign:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param String $editableContent
     *
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
     * Creates a form to edit a PublisherTypes entity.
     *
     * @param PublisherDesign $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PublisherDesign $entity)
    {
        $form = $this->createForm(
            new PublisherDesignType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_publisher_design_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing PublisherDesigns entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PublisherDesign $entity */
        $entity = $em->getRepository('OjsJournalBundle:PublisherDesign')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->setContent(
                $this->prepareDesignContent($entity->getEditableContent())
            );

            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_admin_publisher_design_edit', ['id' => $id]);
        }

        return $this->render(
            'OjsAdminBundle:AdminPublisherDesign:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * @param Request         $request
     * @param PublisherDesign $entity
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws TokenNotFoundException
     */
    public function deleteAction(Request $request, PublisherDesign $entity)
    {
        if (!$this->isGranted('DELETE', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $this->throw404IfNotFound($entity);
        $em = $this->getDoctrine()->getManager();

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_admin_publisher_design'.$entity->getId());
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException('Token Not Found!');
        }
        $em->remove($entity);
        $em->flush();
        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_admin_publisher_design_index');
    }
}
