<?php

namespace Skyflow\DAO;

use Skyflow\Domain\Mapping;

class MappingDAO extends DAO {

    private $eventDAO;
    private $flowDAO;

    public function setEventDAO(EventDAO $eventDAO){
        $this->eventDAO = $eventDAO;
    }

    public function setFlowDAO(FlowDAO $flowDAO){
        $this->flowDAO = $flowDAO;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOne($id){
        $sql = $this->getDb()->prepare("select * from mapping where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $mapping = $sql->fetch();

        if($mapping){
                return $association;
        }
    }

    /**
     * @param $id_user
     * @return array
     */
    public function findAllByUser($id_user){

        $sql = "select * from mapping where id_user =?";
        $result = $this->getDb()->fetchAll($sql,array($id_user));

        $mappings = array();
        foreach ($result as $row) {
            $mappingId = $row['id'];
            $mappings[$mappingId] = $this->buildDomainObject($row);
        }
        return $mappings;
    }


    public function save(Mapping $mapping){
        $mappingData = array(
            'id_event' => $mapping->getEvent()->getId(),
            'id_flow' => $mapping->getFlow()->getId(),
            'id_user' => $mapping->getIdUser(),
        );
        if($mapping->getId()){
            $this->getDb()->update('mapping',$mappingData, array('id' => $mapping->getId()));

        }else{
            $this->getDb()->insert('mapping',$mappingData);
            $id = $this->getDb()->lastInsertId();
            $mapping->setId($id);
        }
    }

    public function findByEventUser($id_event,$id_user){
        $sql = $this->getDb()->prepare("select * from mapping where id_event = ? and id_user = ?");
        $sql->bindValue(1,$id_event);
        $sql->bindValue(2,$id_user);
        $sql->execute();
        $mapping = $sql->fetch();

        if($mapping){
            return $mapping;
        }

    }
    /**
     * @param $id
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id){
        $this->getDb()->delete('mapping',array('id' => $id));
    }


    /**
     * @param $row containing the event data
     * @return Mapping
     */
    protected function buildDomainObject($row) {
        $mapping = new Mapping();
        $mapping->setId($row['id']);
        $mapping->setIdUser($row['id_user']);

        if (array_key_exists('id_event', $row)) {
            // Find and set the associated article
            $eventId = $row['id_event'];
            $event = $this->eventDAO->findOneById($eventId);
            $mapping->setEvent($event);
        }
        if(array_key_exists('id_flow',$row)){
            $flowId = $row['id_flow'];
            $flow = $this->flowDAO->findOneById($flowId);
            $mapping->setFlow($flow);
        }
        return $mapping;
    }
}