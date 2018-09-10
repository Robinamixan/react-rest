<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

class RestController extends FOSRestController
{
    /**
     * @Annotations\Get(
     *     path="/ping"
     * )
     */
	public function getAction()
	{
		return new JsonResponse('pong');
	}
}
