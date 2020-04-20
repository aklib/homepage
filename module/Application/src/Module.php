<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application;

use Laminas\I18n\View\Helper\Translate;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Application\View\Helper\Params;
use Application\View\Helper\GetServiceManager;
use Laminas\ServiceManager\ServiceManager;

class Module {

    public function getConfig(): array {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() {
        /** @noinspection ClassConstantCanBeUsedInspection */
        return [
            'factories' => [
                'translator' => TranslatorServiceFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig() {
        return [
            'factories'      => [
                // the array key here is the name you will call the view helper by in your view scripts  
                'params' => function (ServiceManager $sm){

                    /** @var HelperPluginManager $helperPluginManager */
                    $app = $sm->get('Application');
                    return new Params($app->getRequest(), $app->getMvcEvent());
                },
                'getServiceManager' => function (ServiceManager $sm){
                    return new GetServiceManager($sm);
                },
                'translate' => function (ServiceManager $sm){
                    /** @var HelperPluginManager $hpm */
                    $app = $sm->get('Application');
                    $routeMatch = $app->getMvcEvent()->getRouteMatch();
                    $locale = 'de_DE';
                    if (!empty($routeMatch)) {
                        $language = strtolower($app->getMvcEvent()->getRouteMatch()->getParam('language'));

                        if (!empty($language)) {
                            $conf = $sm->get('Config');
                            $available = $conf['translator']['available'];

                            if (is_array($available)) {
                                foreach($available as $loc => $name) {
                                    if (stripos($loc, $language) === 0) {
                                        $locale = $loc;
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $helper = new Translate();
                    /** @var type $translator */
                    $translator = $app->getServiceManager()->get('translator');
                    $translator->setLocale($locale);
                    $helper->setTranslator($translator);
                    return $helper;
                },
            ],
        ];
    }

}
