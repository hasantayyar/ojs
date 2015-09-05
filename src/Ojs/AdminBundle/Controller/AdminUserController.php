<?php

namespace Ojs\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\ORMException;
use Ojs\AdminBundle\Form\Type\ChangePasswordType;
use Ojs\AdminBundle\Form\Type\UpdateUserType;
use Ojs\AdminBundle\Form\Type\UserType;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\UserBundle\Entity\User;
use Ojs\UserBundle\Entity\UserRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * User administration controller.
 */
class AdminUserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        if (!$this->isGranted('VIEW', new User())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        $source = new Entity('OjsUserBundle:User');
        $grid = $this->get('grid');
        $gridAction = $this->get('grid_action');
        $grid->setSource($source);

        $passwordAction = new RowAction('<i class="fa fa-key"></i>', 'ojs_admin_user_password');
        $passwordAction->setRouteParameters('id');
        $passwordAction->setAttributes(
            [
                'class' => 'btn btn-info btn-xs',
                'data-toggle' => 'tooltip',
                'title' => $this->get('translator')->trans('title.password_change'),
            ]
        );

        $actionColumn = new ActionsColumn('actions', 'actions');
        $rowAction[] = $gridAction->switchUserAction('ojs_public_index', ['username']);
        $rowAction[] = $gridAction->showAction('ojs_admin_user_show', 'id');
        $rowAction[] = $gridAction->editAction('ojs_admin_user_edit', 'id');
        $rowAction[] = $passwordAction;
        $rowAction[] = $gridAction->userBanAction();
        $rowAction[] = $gridAction->deleteAction('ojs_admin_user_delete', 'id');

        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);

        return $grid->getGridResponse('OjsAdminBundle:AdminUser:index.html.twig', ['grid' => $grid]);
    }

    /**
     * Creates a new User entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('CREATE', new User())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new User();
        $form = $this->createCreateForm($entity)
            ->add('create', 'submit', array('label' => 'c'));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($password);
            $em->persist($entity);
            $em->flush();

            $this->successFlashBag('successful.create');

            return $this->redirectToRoute(
                'ojs_admin_user_show',
                [
                    'id' => $entity->getId(),
                ]
            );
        }

        return $this->render(
            'OjsAdminBundle:AdminUser:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(
            new UserType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_user_create'),
                'method' => 'POST',
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @return Response
     */
    public function newAction()
    {
        if (!$this->isGranted('CREATE', new User())) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $entity = new User();
        $form = $this->createCreateForm($entity)
            ->add('create', 'submit', array('label' => 'c'));

        return $this->render(
            'OjsAdminBundle:AdminUser:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsUserBundle:User')->find($id);

        if (!$this->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        $this->throw404IfNotFound($entity);

        $token = $this
            ->get('security.csrf.token_manager')
            ->refreshToken('ojs_admin_user'.$entity->getId());

        return $this->render(
            'OjsAdminBundle:AdminUser:show.html.twig',
            ['entity' => $entity, 'token' => $token]
        );
    }

    /**
     * @param bool $username
     *
     * @return Response
     */
    public function profileAction($username = false)
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository('OjsUserBundle:User');
        $sessionUser = $this->getUser();
        /** @var User $user */
        $user = $username ?
            $userRepo->findOneBy(array('username' => $username)) :
            $sessionUser;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsUserBundle:User')->find($user->getId());
        $this->throw404IfNotFound($entity);

        return $this->render(
            'OjsUserBundle:User:profile.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => array(),
                'me' => ($sessionUser == $user),
            )
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OjsUserBundle:User')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        $editForm = $this->createEditForm($entity)
                        ->add('save', 'submit');

        return $this->render(
            'OjsAdminBundle:AdminUser:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(
            new UpdateUserType(),
            $entity,
            array(
                'action' => $this->generateUrl('ojs_admin_user_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $entity */
        $entity = $em->getRepository('OjsUserBundle:User')->find($id);
        $this->throw404IfNotFound($entity);
        if (!$this->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        $oldPassword = $entity->getPassword();
        $editForm = $this->createEditForm($entity)
            ->add('save', 'submit');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $password = $entity->getPassword();
            if (empty($password)) {
                $entity->setPassword($oldPassword);
            } else {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
            }

            $em->flush();

            $this->successFlashBag('successful.update');

            return $this->redirectToRoute('ojs_admin_user_edit', ['id' => $id]);
        }

        return $this->render(
            'OjsAdminBundle:AdminUser:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a User entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $entity */
        $entity = $em->getRepository('OjsUserBundle:User')->find($id);
        if (!$this->isGranted('DELETE', $entity)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('notFound'));
        }

        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('ojs_admin_user'.$id);
        if ($token != $request->get('_token')) {
            throw new TokenNotFoundException('Token Not Found!');
        }

        $entity->setEnabled(false);
        $em->remove($entity);
        $em->flush();

        $this->successFlashBag('successful.remove');

        return $this->redirectToRoute('ojs_admin_user_index');
    }

    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function blockAction($id)
    {
        /* @var User $user */
        $em = $this->getDoctrine()->getManager();
        $user = $em->find('OjsUserBundle:User', $id);

        if (!$this->isGranted('EDIT', $user)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        if (!$user) {
            throw new NotFoundResourceException('User not found.');
        }

        $user->setEnabled(false);
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('ojs_admin_user_index'));
    }

    /**
     * @param $id
     *
     * @return RedirectResponse
     *
     * @throws ORMException
     */
    public function unblockAction($id)
    {
        /* @var User $user */
        $em = $this->getDoctrine()->getManager();
        $user = $em->find('OjsUserBundle:User', $id);

        if (!$this->isGranted('EDIT', $user)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        if (!$user) {
            throw new NotFoundResourceException('User not found.');
        }

        $user->setEnabled(true);
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('ojs_admin_user_unblock'));
    }

    public function changePasswordAction(Request $request, $id)
    {
        /* @var User $user */
        $em = $this->getDoctrine()->getManager();
        $user = $em->find('OjsUserBundle:User', $id);

        if (!$this->isGranted('EDIT', $user)) {
            throw new AccessDeniedException('You are not authorized for this page!');
        }

        $form = $this->createForm(new ChangePasswordType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $form->get('password')->getData();
            $manipulator = $this->get('fos_user.util.user_manipulator');
            $manipulator->changePassword($user->getUsername(), $password);
            $this->successFlashBag('successful.update');
        } elseif ($request->isMethod('POST')) {
            $this->successFlashBag('user.change_password_fail');
        }

        return $this->render('OjsAdminBundle:AdminUser:password.html.twig', ['form' => $form->createView()]);
    }
}
