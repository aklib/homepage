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

use Laminas\I18n\View\Helper\Translate as LaminasTranslate;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\I18n\Exception;
use Laminas\ServiceManager\ServiceManager;
/**
 * View helper for translating messages.
 */
class Translate extends LaminasTranslate {

    protected $replacements = [];
    protected $queries = [];
    /**
     *
     * @var ServiceManager 
     */
    protected $sm;

    function __construct(TranslatorInterface $translator, ServiceManager $sm) {
        $this->setTranslator($translator);
        $this->setServiceManager($sm);        
        /** @var ServiceManager $sm */        
        $queries = $sm->get('Config')['translator']['queries'];
        if(empty($queries)){
            return;
        }
        $pattern = '<a class="tooltip-link" href="javascript:;" data-html="true" data-toggle="tooltip" data-language="{lang}">{query}</a>';        
        foreach($queries as $query) {
            $this->replacements[] = str_replace(['{query}', '{lang}'], [$query, $this->getTranslator()->getLocale()], $pattern);
            $this->queries[] = '/\b' . $query . '\b/u';
        }
    }

    /**
     * Translate a message
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @throws Exception\RuntimeException
     * @return string
     */
    public function __invoke($message, $textDomain = null, $locale = null) {
        $m = parent::__invoke($message, $textDomain, $locale);
        if (!empty($this->queries)) {
            $m = preg_replace($this->queries, $this->replacements, $m, 1);
        }
        return $m;
    }

    /**
     * gets ServiceManager
     * @return ServiceManager
     */
    protected function getServiceManager(): ServiceManager {
        return $this->sm;
    }

    /**
     * Sets ServiceManager
     * @param ServiceManager $serviceManager
     * @return void
     */
    protected function setServiceManager(ServiceManager $serviceManager): void {
        $this->sm = $serviceManager;
    }
}
