<?php

namespace Post\Service;

use Post\Entity\Post;
use Doctrine\ORM\EntityManager;

class API
{

    public $sm;
    // set our service locator so we can access the DAOs elsewhere.
    public function __construct($sm) {
        $this->sm = $sm;
    }

    protected $em;
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    /**
     * list all the posts
     * @return array
     */
    public function listAll()
    {
        return array(
            'posts' => $this->getEntityManager()->getRepository('Post\Entity\Post')->findall()
        );

    }


    /**
     * Returns all the API description
     * @return string a string result
     */
    public function testFunction()
    {

        return 'this is a test function of class API !';

    }



}