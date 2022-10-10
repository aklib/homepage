<?php

/**
 * $Id:$
 * $HeadURL:$
 * $Date:$
 * $Author:$
 * $Revision: $
 *
 * Translate.php
 *
 * @since 01.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\ServiceManager\ServiceManager;
/**
 * View helper for translating messages.
 */
class ServiceManagerAware extends AbstractHelper {
    protected $sm;
    /**
     * Constructor
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm) {
        $this->sm = $sm;
    }
    
    /**
     * Gets a ServiceManager
     * @return ServiceManager
     */
    public function __invoke() {        
        return $this->sm;
    }
}
