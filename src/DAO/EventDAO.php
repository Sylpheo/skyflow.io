<?php

namespace exactSilex\DAO;


use exactSilex\Domain\Event;

class EventDAO extends DAO {


	public function findOne($event,$idUser){
/*		$sql = "select * from event where event =? and id_user=?";
		$row = $this->getDb()->fetchAssoc($sql,array($event,$idUser));*/

		$sql = $this->getDb()->prepare("select * from event where event = ? and id_user = ?");
		$sql->bindValue(1,$event);
		$sql->bindValue(2,$idUser);
		$sql->execute();
		$event = $sql->fetchAll();

		
		if($event){
			return $event;
		}
	}

	public function findAllByUser($id_user){	

		$sql = "select * from event where id_user =?";
		$result = $this->getDb()->fetchAll($sql,array($id_user));

		$events = array();
		foreach ($result as $row) {
			$eventId = $row['id'];
			$events[$eventId] = $this->buildDomainObject($row);
		}
		return $events;
	}

	public function save(Event $event){
		$eventData = array(
			'event' => $event->getEvent(),
			'triggerSend' => $event->getTriggerSend(),
			'id_user' => $event->getIdUsers(),
			);
		if($event->getId()){
			$this->getDb()->update('event',$eventData, array('id' => $event->getId()));

		}else{
			$this->getDb()->insert('event',$eventData);
			$id = $this->getDb()->lastInsertId();
			$event->setId($id);
		}
	}

	public function delete($id){
		$this->getDb()->delete('event',array('id' => $id));
	}


	protected function buildDomainObject($row) {
		$event = new Event();
		$event->setId($row['id']);
		$event->setEvent($row['event']);
		$event->setTriggerSend($row['triggerSend']);
		$event->setIdUsers($row['id_user']);
		return $event;
    }
}