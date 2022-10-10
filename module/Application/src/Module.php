<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application;

//use Laminas\I18n\View\Helper\Translate;
use Application\View\Helper\Translate;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Laminas\I18n\Translator\Translator;
use Application\View\Helper\Params;
use Application\View\Helper\ServiceManagerAware;
use Laminas\ServiceManager\ServiceManager;
use Interop\Container\ContainerInterface as InteropContainerInterface;
//use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Mvc\Controller\AbstractActionController;

class Module {

    public function getConfig(): array {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() {
        /** @noinspection ClassConstantCanBeUsedInspection */
        return [
            'factories' => [
                'MvcTranslator' => TranslatorServiceFactory::class,
                'translator'    => function (InteropContainerInterface $sm){
                    /** @var Translator $translator */
                    $translator = $sm->get('MvcTranslator');
                    $translator->setLocale($this->getLocale($sm));
                    return $translator;
                    
                },
            ],
        ];
    }

    /**
     * Configure controller instances
     * @return void
     */
    public function getControllerConfig() {
        return [
            'initializers' => [
                'sm' => function (InteropContainerInterface $sm, AbstractActionController $instance){
                    if (method_exists($instance, 'setServiceManager')) {
                        $instance->setServiceManager($sm);
                    }
                }
            ]
        ];
    }

    /**
     * Init view helpers
     * @return ViewHelper
     */
    public function getViewHelperConfig() {
        return [
            'factories'         => [
                // the array key here is the name you will call the view helper by in your view scripts  
                'params' => function (InteropContainerInterface $sm){

                    /** @var HelperPluginManager $helperPluginManager */
                    $app = $sm->get('Application');
                    return new Params($app->getRequest(), $app->getMvcEvent());
                },
                'getServiceManager' => function (InteropContainerInterface $sm){
                    return new ServiceManagerAware($sm);
                },
                'translate' => function (InteropContainerInterface $sm){
                    /** @var HelperPluginManager $hpm */
                    $app = $sm->get('Application');
                    /** @var type $translator */
                    $translator = $app->getServiceManager()->get('translator');                    
                    $helper = new Translate($translator, $sm);                    
                    return $helper;
                },
            ],
        ];
    }

    /**
     * Gets current locale from route
     * @param ServiceManager $sm
     * @return string[2]
     */
    private function getLocale(InteropContainerInterface $sm) {
        $locale = 'de';
        $routeMatch = $sm->get('Application')->getMvcEvent()->getRouteMatch();
        if (!empty($routeMatch)) {
            $language = strtolower($routeMatch->getParam('language'));
            if (!empty($language)) {
                $available = $sm->get('Config')['translator']['available'];
                if (is_array($available)) {
                    foreach(array_keys($available) as $loc) {
                        if (stripos($loc, $language) === 0) {
                            $locale = $loc;
                            break;
                        }
                    }
                }
            }
        }
        return $locale;
    }

}
