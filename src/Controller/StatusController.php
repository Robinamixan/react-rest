<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @Rest\Route("/api/v1")
 */
class StatusController extends FOSRestController
{
    /**
     * @Rest\Get("/status")
     * @Rest\View()
     */
    public function status()
    {
        return [
            'status' => 'ok',
        ];
    }
}
