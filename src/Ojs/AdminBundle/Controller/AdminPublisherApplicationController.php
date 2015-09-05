<?php

namespace Ojs\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Ojs\AdminBundle\Form\Type\PublisherApplicationType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\CoreBundle\Params\CommonParams;
use Ojs\JournalBundle\Entity\Publisher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * Publisher controller.
 */
class AdminPublisherApplicationController extends Controller
{
    public function indexAction()
    {
        $data = array();
        $source = new Entity('OjsJournalBundle:Publisher', 'application');
        $tableAlias = $source->getTableAlias();

        $source->manipulateQuery(
            function (QueryBuilder $query) use ($tableAlias) {
                $query
                    ->andWhere($tableAlias.'.status = :status')
                    ->setParameter('status', 0);

                return $query;
            }
        );

        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('grid_action');
        $grid->getColumn('status')->manipulateRenderCell(
            function ($value) {
                return $this->get('translator')->trans(
                    CommonParams::publisherStatus($value)
                );
            }
        );

        $rowAction = array();
        $saveAction = new RowAction('<i class="fa fa-save"></i>', 'ojs_admin_application_publisher_save');
        $saveAction->setRouteParameters(['id']);
        $saveAction->setAttributes([
            'class' => 'btn btn-primary btn-xs',
            'title' => $this->get('translator')->trans('institute.merge_as_new_institute'),
        ]);

        $rowAction[] = $saveAction;
        $rowAction[] = $gridAction->showAction('ojs_admin_application_publisher_show', 'id');
        $rowAction[] = $gridAction->editAction('ojs_admin_application_publisher_edit', 'id');
        $rowAction[] = $gridAction->deleteAction('ojs_admin_application_publisher_delete', 'id');

        $actionColumn = new ActionsColumn('actions', 'actions');
        $actionColumn->setRowActions($rowAction);

        $grid->addColumn($actionColumn);
        $data['grid'] = $grid;

        return $grid->getGridResponse('OjsAdminBundle:AdminApplication:publisher.html.twig', $data);
    }

    public function detailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em
            ->getRepository('OjsJournalBundle:Publisher')
            ->findOneBy(['status' => 0, 'id' => $id]);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->render('OjsAdminBundle:AdminApplication:publisher_detail.html.twig', ['entity' => $entity]);
    }

    public function editAction($id)
    {
        /* @var Publisher $entity */
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsJournalBundle:Publisher')->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $action = $this->generateUrl('ojs_admin_application_publisher_update', ['id' => $entity->getId()]);
        $form = $this->createForm(new PublisherApplicationType(), $entity, ['action' => $action])->add('update', 'submit');

        return $this->render(
            'OjsAdminBundle:AdminApplication:publisher_edit.html.twig',
            ['form' => $form->createView(), 'entity' => $entity]
        );
    }

    public function updateAction(Request $request, $id)
    {
        /* @var Publisher $entity */
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsJournalBundle:Publisher')->find($id);
        $this->throw404IfNotFound($entity);

        $form = $this->createForm(new PublisherApplicationType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            $this->successFlashBag('successful.update');

            return $this->redirect($this->generateUrl('ojs_admin_application_publisher_index'));
        }

        return $this->render(
            'OjsAdminBundle:AdminApplication:publisher_edit.html.twig',
            ['form' => $form->createView(), 'entity' => $entity]
        );
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Publisher $entity */
        $entity = $em->getRepository('OjsJournalBundle:Publisher')->find($id);

        $this->throw404IfNotFound($entity);

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_admin_application'.$id);
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException('Token Not Found!');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->get('router')->generate('ojs_admin_application_publisher_index'));
    }

    public function saveAction($id)
    {
        /* @var Publisher $entity */
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsJournalBundle:Publisher')->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $entity->setStatus(1);
        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute('ojs_admin_application_publisher_edit', ['id' => $entity->getId()]);
    }

    public function rejectAction($id)
    {
        /* @var Publisher $entity */
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsJournalBundle:Publisher')->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $entity->setStatus(-1);
        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute('ojs_admin_application_publisher_edit', ['id' => $entity->getId()]);
    }
}
