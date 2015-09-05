<?php

namespace Ojs\JournalBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Ojs\AdminBundle\Form\Type\PublisherDesignType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Publisher;
use Ojs\JournalBundle\Entity\PublisherDesign;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * PublisherDesign controller.
 */
class ManagerPublisherDesignController extends Controller
{
    /**
     * Lists all PublisherDesigns entities.
     */
    public function indexAction($publisherId)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $source = new Entity('OjsJournalBundle:PublisherDesign');
        $alias = $source->getTableAlias();
        $source->manipulateQuery(
            function (QueryBuilder $qb) use ($publisher, $alias) {
                $qb->andWhere($alias.'.publisher = :publisher')
                    ->setParameter('publisher', $publisher);
            }
        );
        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');

        $actionColumn = new ActionsColumn('actions', 'actions');

        $rowAction[] = $gridAction->showAction('ojs_publisher_manager_design_show', ['publisherId' => $publisher->getId(), 'id']);
        $rowAction[] = $gridAction->editAction('ojs_publisher_manager_design_edit', ['publisherId' => $publisher->getId(), 'id']);
        $rowAction[] = $gridAction->deleteAction('ojs_publisher_manager_design_delete', ['publisherId' => $publisher->getId(), 'id']);

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        $data = [];
        $data['grid'] = $grid;
        $data['publisher'] = $publisher;

        return $grid->getGridResponse('OjsJournalBundle:ManagerPublisherDesign:index.html.twig', $data);
    }

    /**
     * Creates a new PublisherDesign entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction($publisherId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new PublisherDesign();
        $form = $this->createCreateForm($entity, $publisher);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPublisher($publisher);
            $entity->setContent(
                $this->prepareDesignContent($entity->getEditableContent())
            );
            $em->persist($entity);
            $em->flush();
            $this->successFlashBag('successful.create');

            return $this->redirectToRoute('ojs_publisher_manager_design_show', ['publisherId' => $publisher->getId(), 'id' => $entity->getId()]);
        }

        return $this->render(
            'OjsJournalBundle:ManagerPublisherDesign:new.html.twig',
            array(
                'entity' => $entity,
                'publisher' => $publisher,
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
    private function createCreateForm(PublisherDesign $entity, $publisher)
    {
        $form = $this->createForm(
            new PublisherDesignType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_publisher_manager_design_create', ['publisherId' => $publisher->getId()]),
                'method' => 'POST',
            )
        );
        $form->remove('publisher');
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
    public function newAction($publisherId)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new PublisherDesign();
        $form = $this->createCreateForm($entity, $publisher);

        return $this->render(
            'OjsJournalBundle:ManagerPublisherDesign:new.html.twig',
            array(
                'entity' => $entity,
                'publisher' => $publisher,
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
    public function showAction($publisherId, PublisherDesign $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_publisher_manager_design'.$entity->getId());

        return $this->render(
            'OjsJournalBundle:ManagerPublisherDesign:show.html.twig',
            [
                'entity' => $entity,
                'publisher' => $publisher,
                'token' => $token,
            ]
        );
    }

    /**
     * Displays a form to edit an existing PublisherDesign entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($publisherId, PublisherDesign $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity->setEditableContent($this->prepareEditContent($entity->getEditableContent()));
        $editForm = $this->createEditForm($entity, $publisher);

        return $this->render(
            'OjsJournalBundle:ManagerPublisherDesign:edit.html.twig',
            array(
                'entity' => $entity,
                'publisher' => $publisher,
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
     * @param PublisherDesign $entity    The entity
     * @param Publisher       $publisher
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PublisherDesign $entity, Publisher $publisher)
    {
        $form = $this->createForm(
            new PublisherDesignType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_publisher_manager_design_update', array('publisherId' => $publisher->getId(), 'id' => $entity->getId())),
                'method' => 'PUT',
            )
        );
        $form->remove('publisher');
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
    public function updateAction(Request $request, $publisherId, PublisherDesign $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $editForm = $this->createEditForm($entity, $publisher);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->setContent(
                $this->prepareDesignContent($entity->getEditableContent())
            );
            $entity->setPublisher($publisher);

            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_publisher_manager_design_edit', [
                'id' => $entity->getId(),
                    'publisherId' => $publisher->getId(),
                ]
            );
        }

        return $this->render(
            'OjsJournalBundle:ManagerPublisherDesign:edit.html.twig',
            array(
                'entity' => $entity,
                'publisher' => $publisher,
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
    public function deleteAction(Request $request, PublisherDesign $entity, $publisherId)
    {
        $em = $this->getDoctrine()->getManager();
        $publisher = $em->getRepository('OjsJournalBundle:Publisher')->find($publisherId);
        $this->throw404IfNotFound($publisher);
        if (!$this->isGrantedForPublisher($publisher)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $this->throw404IfNotFound($entity);
        $em = $this->getDoctrine()->getManager();

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_publisher_manager_design'.$entity->getId());
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException('Token Not Found!');
        }
        $em->remove($entity);
        $em->flush();
        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_publisher_manager_design_index', ['publisherId' => $publisherId]);
    }
}
