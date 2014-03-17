<?php
namespace Joduino\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Select;
 class EnvironmentTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getHistory($nbelt = 20)
     {
	$resultSet = $this->tableGateway->select(
		function (Select $select) {
     			//$select->where->like('name', 'Brit%');
     			$select->order('msgdate DESC')->limit(20);
			}
		);
     	return $resultSet;
     }


     public function getEnvironment($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveEnvironment(Environment $environment)
     {
         $data = array(
		'sensor_id' => $environment->sensor_id,
		'humidity' => $environment->humidity,
		'temperature' => $environment->temperature,
		'photoresistance' => $environment->photoresistance,
		'msg' => $environment->msg,
		'msgdate' => $environment->msgdate,
         );

         $id = (int) $environment->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getEnvironment($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Environment id does not exist');
             }
         }
     }


     public function deleteEnvironment($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }