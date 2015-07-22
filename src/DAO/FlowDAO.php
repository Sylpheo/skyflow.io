<?php

namespace skyflow\DAO;


use skyflow\Domain\Flow;

class FlowDAO extends DAO {


    /**
     * @param $name
     * @param $idUser
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOne($name,$idUser){
        /*		$sql = "select * from event where event =? and id_user=?";
                $row = $this->getDb()->fetchAssoc($sql,array($event,$idUser));*/

        $sql = $this->getDb()->prepare("select * from flow where name = ? and id_user = ?");
        $sql->bindValue(1,$name);
        $sql->bindValue(2,$idUser);
        $sql->execute();
        $flow = $sql->fetch();

        if($flow){
            return $flow;
        }
    }


    public function findOneById($id){
        /*		$sql = "select * from event where event =? and id_user=?";
                $row = $this->getDb()->fetchAssoc($sql,array($event,$idUser));*/

        $sql = $this->getDb()->prepare("select * from flow where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $flow = $sql->fetch();

        if($flow){
            return $flow;
        }
    }

    /**
     * @param $id_user
     * @return array
     */
    public function findAllByUser($id_user){

        $sql = "select * from flow where id_user =?";
        $result = $this->getDb()->fetchAll($sql,array($id_user));

        $flows = array();
        foreach ($result as $row) {
            $flowId = $row['id'];
            $flows[$flowId] = $this->buildDomainObject($row);
        }
        return $flows;
    }

    /**
     * @param Flow $flow
     */
    public function save(Flow $flow){
        $flowData = array(
            'name' => $flow->getName(),
            'class' => $flow->getClass(),
            'documentation' => $flow->getDocumentation(),
            'id_user' => $flow->getIdUser(),
        );
        if($flow->getId()){
            $this->getDb()->update('flow',$flowData, array('id' => $flow->getId()));

        }else{
            $this->getDb()->insert('flow',$flowData);
            $id = $this->getDb()->lastInsertId();
            $flow->setId($id);
        }
    }

    /**
     * @param $id
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id){
        $this->getDb()->delete('flow',array('id' => $id));
    }


    /**
     * @param $row containing the event data
     * @return Event
     */
    protected function buildDomainObject($row) {
        $flow = new Flow();
        $flow->setId($row['id']);
        $flow->setName($row['name']);
        $flow->setClass($row['class']);
        $flow->setDocumentation($row['documentation']);
        $flow->setIdUser($row['id_user']);
        return $flow;
    }
}