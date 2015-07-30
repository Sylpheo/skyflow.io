<?php

namespace skyflow\DAO;

use skyflow\Domain\Wave_request;

class Wave_requestDAO extends DAO {

    public function findAllByUser($id_user){
        $sql = "select * from wave_request where id_user =? limit 10";
        $request = $this->getDb()->fetchAll($sql,array($id_user));

        $requests = array();
        foreach ($request as $row) {
            $requestId = $row['id'];
            $requests[$requestId] = $this->buildDomainObject($row);
        }
        return $requests;

    }

    public function findByRequest($request,$id_user){

        $sql = $this->getDb()->prepare("select * from wave_request where request = ? and id_user =?");
        $sql->bindValue(1,$request);
        $sql->bindValue(2,$id_user);
        $sql->execute();
        $result = $sql->fetch();

        if($result){
            return $this->buildDomainObject($result);
        }
    }

    public function findById($id){
        $sql = $this->getDb()->prepare("select * from wave_request where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $request = $sql->fetch();

        if($request){
            return $this->buildDomainObject($request);
        }
    }

    public function save(Wave_request $waverequest){
        $wave_requestData = array (
            'request' => $waverequest->getRequest(),
            'id_user' => $waverequest->getIdUser(),
        );

        if($waverequest->getId()){
            $this->getDb()->update('wave_request',$wave_requestData,array('id'=>$waverequest->getId()));
        }else{
            $this->getDb()->insert('wave_request',$wave_requestData);
            $id = $this->getDb()->lastInsertId();
            $waverequest->setId($id);
        }
    }

    protected function buildDomainObject($row) {

        $waveRequest = new Wave_request();
        $waveRequest->setId($row['id']);
        $waveRequest->setRequest($row['request']);
        $waveRequest->setIdUser($row['id_user']);
        return $waveRequest;
    }
}