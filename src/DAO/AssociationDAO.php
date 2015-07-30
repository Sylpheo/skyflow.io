<?php

namespace skyflow\DAO;

use skyflow\Domain\Association;

class AssociationDAO extends DAO {

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
        $sql = $this->getDb()->prepare("select * from association where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $association = $sql->fetch();

        if($association){
                return $association;
        }
    }

    /**
     * @param $id_user
     * @return array
     */
    public function findAllByUser($id_user){

        $sql = "select * from association where id_user =?";
        $result = $this->getDb()->fetchAll($sql,array($id_user));

        $associations = array();
        foreach ($result as $row) {
            $associationId = $row['id'];
            $associations[$associationId] = $this->buildDomainObject($row);
        }
        return $associations;
    }


    public function save(Association $association){
        $associationData = array(
            'id_event' => $association->getEvent()->getId(),
            'id_flow' => $association->getFlow()->getId(),
            'id_user' => $association->getIdUser(),
        );
        if($association->getId()){
            $this->getDb()->update('flow',$associationData, array('id' => $association->getId()));

        }else{
            $this->getDb()->insert('association',$associationData);
            $id = $this->getDb()->lastInsertId();
            $association->setId($id);
        }
    }

    public function findByEventUser($id_event,$id_user){
        $sql = $this->getDb()->prepare("select * from association where id_event = ? and id_user = ?");
        $sql->bindValue(1,$id_event);
        $sql->bindValue(2,$id_user);
        $sql->execute();
        $association = $sql->fetch();

        if($association){
            return $association;
        }

    }
    /**
     * @param $id
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id){
        $this->getDb()->delete('association',array('id' => $id));
    }


    /**
     * @param $row containing the event data
     * @return Event
     */
    protected function buildDomainObject($row) {
        $association = new Association();
        $association->setId($row['id']);
        $association->setIdUser($row['id_user']);
       /* $association->setIdEvent($row['id_event']);
        $association->setIdFlow($row['id_flow']);*/
        if (array_key_exists('id_event', $row)) {
            // Find and set the associated article
            $eventId = $row['id_event'];
            $event = $this->eventDAO->findOneById($eventId);
            $association->setEvent($event);
        }
        if(array_key_exists('id_flow',$row)){
            $flowId = $row['id_flow'];
            $flow = $this->flowDAO->findOneById($flowId);
            $association->setFlow($flow);
        }
        return $association;
    }
}