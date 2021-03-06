<?php

namespace Ojs\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ojs\JournalBundle\Entity\Journal;
use Ojs\JournalBundle\Entity\JournalUser;
use Ojs\JournalBundle\Entity\JournalUserRepository;
use Ojs\UserBundle\Entity\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleRestController extends FOSRestController
{
    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get Role Action",
     *  filters={
     *      {"name"="id", "dataType"="integer"}
     *  }
     * )
     * @Get("/role/{id}")
     *
     * @param $id
     * @return object
     */
    public function getRoleAction($id)
    {
        $role = $this->getDoctrine()->getRepository('OjsUserBundle:Role')->find($id);
        if (!is_object($role)) {
            throw new HttpException(404, 'Not found. The record is not found or route is not defined.');
        }

        return $role;
    }

    /**
     * @todo not implemented yet
     * @ApiDoc(
     *  resource=true,
     *  description="Get Users with this role for this journal",
     *  parameters={
     *      {
     *          "name"="role_id",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="role id"
     *      },{
     *          "name"="journal_id",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="role id"
     *      },{
     *          "name"="page",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="offset page"
     *      },
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="limit"
     *      }
     *  }
     * )
     * @Get("/role/{role}/journal/{journal}/users")
     *
     * @param  Role              $role
     * @param  Journal           $journal
     * @return JournalUser[]
     */
    public function getJournalRoleUsersAction(Role $role, Journal $journal)
    {
        /** @var JournalUserRepository $journalUserRepo */
        /** @var JournalUser[] $result */

        $journalUserRepo = $this
            ->getDoctrine()
            ->getRepository('OjsJournalBundle:JournalUser');
        $result = $journalUserRepo->findByRoles($role, $journal);

        if (!is_array($result)) {
            throw new HttpException(404, 'Not found. The record is not found or route is not defined.');
        }

        return $result;
    }
    /*
        private function notFound()
        {
            throw new HttpException(404, 'Not found. The record is not found or route is not defined.');
        }
    */
}
