<?php
/**
* Joduino (https://github.com/Jodaille)
*
* @link      https://github.com/Jodaille/joduino for the canonical source repository
* @copyright Copyright (c) 2014 Jodaille (http://jodaille.org)
* @license   New BSD License
*/

namespace Joduino\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Prompt;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Select;
use Zend\Console\ColorInterface;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class CronController extends AbstractActionController
{
  protected $environmentTable;

  public function loadJsonLogAction()
  {
    $request = $this->getRequest();
    if (!$request instanceof ConsoleRequest){
      throw new \RuntimeException('You can only use this action from a console!');
    }

    $verbose    = $request->getParam('verbose', false);
    $filepath   = $request->getParam('filepath');
    $sensor_id   = $request->getParam('sensor_id', 1);



    $arduinoJsonLog = $this->getServiceLocator()->get('Joduino\Model\ArduinoJsonLog');

    $arduinoJsonLog->importFile($filepath, $sensor_id);

  }

  public function indexAction()
  {
    $arduinoIcc = $this->getServiceLocator()->get('Joduino\Model\ArduinoIcc');
    $foam 	= $this->getRequest()->getQuery('foam');
    $fan 	= $this->getRequest()->getQuery('fan');

    if($fan == 'fan_on')
    {
      $json = $arduinoIcc->sendMsgToArduino(11);
    }
    else
    {
      $json = $arduinoIcc->sendMsgToArduino(12);
    }

    if($foam == 'foam_on')
    {
      $json = $arduinoIcc->sendMsgToArduino(14);
    }
    else
    {
      $json = $arduinoIcc->sendMsgToArduino(15);
    }

    return new ViewModel(array('data' => $json, 'fan' => $fan, 'foam' => $foam));
  }

  public function foamAction()
  {
    $request = $this->getRequest();

    if (!$request instanceof ConsoleRequest){
      throw new \RuntimeException('You can only use this action from a console!');
    }

    $verbose     = $request->getParam('verbose', false);
    $state   = $request->getParam('state');

    $arduinoIcc = $this->getServiceLocator()->get('Joduino\Model\ArduinoIcc');
    $arduinoIcc->changeFoamState($state);

    return "foam $state!\n";
  }

  public function fanAction()
  {
    $request = $this->getRequest();

    if (!$request instanceof ConsoleRequest){
      throw new \RuntimeException('You can only use this action from a console!');
    }
    $verbose     = $request->getParam('verbose', false);
    $state   = $request->getParam('state');

    $arduinoIcc = $this->getServiceLocator()->get('Joduino\Model\ArduinoIcc');
    $arduinoIcc->changeFanState($state);

    return "fan $state!\n";
  }

  public function logenvironmentAction()
  {
    $arduinoIcc = $this->getServiceLocator()->get('Joduino\Model\ArduinoIcc');

    $json = $arduinoIcc->sendMsgToArduino(10, false);

    return "$json\n";
  }


  public function getEnvironmentTable()
  {
    if (!$this->environmentTable) {
      $sm = $this->getServiceLocator();
      $this->environmentTable = $sm->get('Joduino\Model\EnvironmentTable');
    }
    return $this->environmentTable;
  }
}
