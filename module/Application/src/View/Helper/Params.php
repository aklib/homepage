<?php

/**
 * $Id:$
 * $HeadURL:$
 * $Date:$
 * $Author:$
 * $Revision: $
 *
 * Params.php
 *
 * @since 09.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper;

use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\RequestInterface;
use Laminas\View\Helper\AbstractHelper;

class Params extends AbstractHelper {
    protected $request;
    protected $event;
    protected $params;

    public function __construct(RequestInterface $request, MvcEvent $event) {
        $this->request = $request;
        $this->event = $event;
    }
    public function __invoke($name = null, $default = null) {
        if(empty($this->params)){
            $this->params = [];
            $this->params = array_replace_recursive(
                        (array)$this->request->getQuery(),
                        (array)$this->request->getPost(),
                        (array)(!is_null($this->event->getRouteMatch()) ? $this->event->getRouteMatch()->getParams(): [])
                    );
        }
        if ($name === null) {
            return $this->params;
        }
        return empty($this->params[$name]) ? $default : $this->params[$name];
    }
}
