<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Controller\Plugin\Translate;

return [
    'router'             => [
        'routes' => [
            'home' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:language]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                        'language'   => 'de',
                    ],
                ],
            ],
            'wiki' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/wiki[/:language]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'wiki',
                        'language'   => 'de',
                    ],
                ],
            ],
        ],
    ],
    'translator'         => [
        'locale'                    => 'de',
        'available'                 => [
            'en' => 'english',
            'de' => 'deutsch',
            'ru' => 'russisch',
        ],
        'queries'                   => ['OOP', 'MAM', 'DAM', 'PIM', 'Maven', 'Hibernate', 'Zend Framework',
            'Doctrine','HTML5', 'HTML', 'XHTML','CSS3', 'CSS', 'Javascript', 'JQuery',
            'API', 'MySQL', 'PostgreSQL', 'FreeBSD', 'SQLite', 'Elasticsearch','Access-Datenbank'],
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => realpath(__DIR__ . '/../language'),
                'pattern'  => '%s.mo',
            ],
        ],
        'event_manager_enabled'     => true
    ],
    'controllers'        => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'controller_plugins' => [
         'invokables' => [
            'translate' => Translate::class,
        ],        
        'aliases'   => [
            'wiki' => Controller\Plugin\Wiki::class,
        ]
    ],
    'view_manager'       => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];
