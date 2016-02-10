<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Mf;


use Mf\Http\Request;
use Mf\Http\Response;

class Application
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}