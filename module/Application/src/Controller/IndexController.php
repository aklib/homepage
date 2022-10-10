<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
//declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Http\Client;
use Laminas\Http\Response;
use Laminas\ServiceManager\ServiceManager;
use FlorianWolters\Component\Core\StringUtils;

class IndexController extends AbstractActionController {

    /**
     *
     * @var ServiceManager 
     */
    protected $sm;

    public function indexAction() {
        return new ViewModel();
    }

    public function wikiAction() {
        $query = $this->prepareQuery($this->params()->fromQuery('query'));
        $language = $this->getServiceManager()->get('translator')->getLocale();
        
        $options = [
            'useragent' => 'WikiBot/1.0 (+http://kisselev.de/)',
            'timeout'   => 30
        ];

        $params = [
            'action'   => 'query',
            'format'   => 'json',
            'list'     => 'search',
            'wordcount' => 6000,
            'maxlag'   => 1,
            'srlimit'  => 1,
            'srsearch' => 'intitle:' . $query,
        ];

              
        $url = 'https://' . $language . '.wikipedia.org/w/api.php';

        $client = new Client($url);
        $client->setParameterGet($params);
        $client->setOptions($options);
        /** @var Response $response */
        $response = $client->send();
        $search = $response->getBody(); //;
        
        $result = \json_decode($search, true)['query']['search'][0];
//        echo '<pre>';
//        print_r(\json_decode($search, true));
//        echo '</pre>';
//        die;
        if (empty($result)) {
            $result = [
                'title'   => $query,
                'snippet' => 'Sorry, no information found'
            ];
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('result', $result);
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    /**
     * Validate a query string from site and gets translated query string if available
     * @param string $query
     * @return string
     */
    protected function prepareQuery($query) {
        if(empty($this->queries)){
            $this->queries = $this->getServiceManager()->get('Config')['translator']['queries'];
        }
        if(!in_array($query, $this->queries)){
            // data has been manipulated
            return 'Unit testing';
        }
        $queryT = $this->translate("#wiki $query");
        if(!StringUtils::startsWith($queryT, '#wiki ')){
            // translated
            $query = $queryT;
        }
        return $query;
    }

    /**
     * gets ServiceManager
     * @return ServiceManager
     */
    function getServiceManager(): ServiceManager {
        return $this->sm;
    }

    /**
     * Sets ServiceManager
     * @param ServiceManager $serviceManager
     * @return void
     */
    function setServiceManager(ServiceManager $serviceManager): void {
        $this->sm = $serviceManager;
    }

}
