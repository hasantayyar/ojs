<?php

namespace Ojs\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use Elastica\Query;
use FOS\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer;
use Symfony\Component\HttpFoundation\Request;

class SearchRestController extends FOSRestController {

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="search Institutions",
     *  parameters={
     * {
     *          "name"="q",
     *          "dataType"="string",
     *          "required"="true",
     *          "description"="search term"
     *      },
     *      {
     *          "name"="journal_id",
     *          "dataType"="integer",
     *          "required"="true",
     *          "description"="list only verified or not"
     *      }
     *  }
     * )
     * @Get("/search/journal/users")
     */
    public function getJournalUsersAction(Request $request)
    {
        $journalId = $request->get('journal_id');
        $q = $request->get('q');
        $search = $this->container->get('fos_elastica.index.search.institution');
        $query = new Query\Bool();
        $must1 = new Query\Match();
        $must1->setField('name', $q);
        $query->addMust($must1);

        if ($verified !== null) {
            $must2 = new Query\Match();
            $must2->setField('verified', $verified);
            $query->addMust($must2);
        }
        $results = $search->search($query);
        $data = [];
        foreach ($results as $result) {
            $data[] = array_merge(array('id' => $result->getId()), $result->getData());
        }
        return $data;
    }

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="search users in username-email-tags and subjects (accepts regex inputs)",
     *  parameters={
     * {
     *          "name"="q",
     *          "dataType"="string",
     *          "required"="true",
     *          "description"="search term"
     *      }
     *  }
     * )
     * @Get("/search/user")
     */
    public function getUsersAction(Request $request)
    {
        $q = $request->get('q');
        $search = $this->container->get('fos_elastica.index.search.user');


        $s1 = new Query\Regexp();
        $s1->setValue('username', $q );

        $s2 =new Query\Regexp();
        $s2->setValue('subjects', $q );

        $s3 =new Query\Regexp();
        $s3->setValue('tags', $q);

        $query = new Query\Bool();
        $query->addShould($s1);
        $query->addShould($s2);
        $query->addShould($s3);

        $results = $search->search($query);
        $data = [];
        foreach ($results as $result) {
            $data[] = array_merge(array('id' => $result->getId()), $result->getData());
        } 
        return $data;
    }

}