<?php

namespace Post\Controller;

use Post\Service\ServiceAPI;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Soap\Wsdl;
use Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\View\Model\ViewModel;

use Zend\Soap\AutoDiscover;
use Zend\Soap\Server;
use Zend\Soap\Client;

use Post\Controller\PostController;
use Post\Service\API;

require_once getcwd() . '/module/Post/src/Post/Service/ServiceAPI.php';
require_once getcwd() . '/module/Post/src/Post/Controller/PostController.php';


class HandlerController extends AbstractActionController
{
    private $_URI;
    private $_WSDL_URI;


    public function indexAction()
    {
        // set up service locator for SoapServe class
        $sm = $this->getServiceLocator();

        // get the current URL so it works in dev, staging, production, etc
        $this->_URI = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/handler";
        $this->_WSDL_URI = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/handler?wsdl";

        // ******** test here *********
//        $this->_URI = 'http://guestbook-soap.local/handler/soap';
//        $this->_WSDL_URI =
        // ******** end test here *********


        // is this a request for the wsdl or a SOAP request?
        if (isset($_GET['wsdl'])) {
            $this->handleWSDL($sm);
        } else {
            $this->handleSOAP($sm);
        }

        // this is required to strip out the layout, otherwise not nice results!
        $result = new ViewModel();
        $result->setTerminal(true);

        return $result;
    }

    private function handleWSDL($sm)
    {
        $autodiscover = new AutoDiscover();

//        $autodiscover->setClass('Post\Service\API')
        $autodiscover->setClass(new API($sm))
//        $autodiscover->setClass(new PostController($sm))
            ->setUri($this->_URI);

        $wsdl = $autodiscover->generate();

        // handle request
        $autodiscover->handle();

//        echo $wsdl->toXml();

//        $wsdl->dump("public/SoapServe.wsdl");
//        $dom = $wsdl->toDomDocument();
    }


    private function handleSOAP($sm)
    {
        $soap = new Server(
            NULL,
            array(
                'wsdl' => $this->_WSDL_URI
            )
        );
        $soap->setWSDLCache(false);


//        $soap->setClass('Post\Service\API');
        $soap->setClass(new API($sm));
//        $soap->setClass(new PostController($sm));

//        $apiObject = $this->serviceLocator->get('Post\Service\API');
//        $soap->setObject($apiObject);

        $soap->handle();
    }

//    public function soapAction()
//    {
//
//        // initialize server and set URI
//
//        // non-wsdl mode
//        $server = new Server(null, array('uri' => "http://guestbook-soap.local/handler/wsdl"));
//
//        // wsdl mode
////        $server = new Server('http://guestbook-soap.local/handler/wsdl');
//
//
//        // set SOAP service class
////        $server->setClass('Post\Service\ServiceAPI');
//
//        // test here
//        $server->setClass('Post\Service\ServiceAPI');
////
////        $apiObject = $this->serviceLocator->get('Post\Controller\PostController');
////        $server->setObject($apiObject);
//
//        // handle request
//        $server->handle();
//
////        $result = $server->handle();
//        exit;
//
//    }


//    public function wsdlAction()
//    {
//        $sm = $this->getServiceLocator();
//
//
//        // set up WSDL auto-discovery
//        $autoDiscover = new AutoDiscover();
//
//        // set SOAP action URI
//        $autoDiscover->setUri('http://guestbook-soap.local/handler/soap');  // which version?
////        $autoDiscover->setUri('http://guestbook-soap.local/handler/wsdl'); //
//
//        // set service name:
//        $autoDiscover->setServiceName('MyHandlerWsdlService');
//
//        // test here
////        $autoDiscover->setClass(new \Post\Controller\PostController($sm));
//        $autoDiscover->setClass('Post\Service\ServiceAPI');
////        $autoDiscover->setClass('Post\Service\API');
//
//        $wsdl = $autoDiscover->generate();
//
//        header('Content-type: application/xml');
//
//        // handle request
//        $autoDiscover->handle();
//
////        $wsdl = $autoDiscover->generate();
////        echo $wsdl->toXml();
//
//        exit;
//
//
//    }

    public function clientAction()
    {

        $options = array(
            'location' => 'http://guestbook-soap.local/handler',
            'uri'      => 'http://guestbook-soap.local/handler',
            'cache_wsdl' => WSDL_CACHE_NONE,
        );

        $client = new Client(null, $options);

//        $client = new Client('http://guestbook-soap.local/handler');


//        $result = $client->testFunction();
        $result = $client->listAll();
//        $result = $client->mengdiTest();


        echo '<pre>';
        var_dump($result);
        echo '</pre>';die;


    }

}