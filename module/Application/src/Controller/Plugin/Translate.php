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
 * @since 13.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Translate extends AbstractPlugin  {
    protected $translate;

    public function __invoke($message, $textDomain = null, $locale = null) {
        if(empty($message)){
            return '';
        }
        if(is_null($this->translate)){
            $this->translate = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('translate');
        }
        $translate = $this->translate;
        return $translate($message, $textDomain, $locale);
    }
}
