<?php

namespace skyflow\DAO;


use skyflow\Domain\Event;

class EventDAO extends DAO {


	/**
	 * @param $event
	 * @param $idUser
	 * @return event
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function findOne($name,$idUser){
/*		$sql = "select * from event where event =? and id_user=?";
		$row = $this->getDb()->fetchAssoc($sql,array($event,$idUser));*/

		$sql = $this->getDb()->prepare("select * from event where name = ? and id_user = ?");
		$sql->bindValue(1,$name);
		$sql->bindValue(2,$idUser);
		$sql->execute();
		$event = $sql->fetch();

		if($event){
			return $event;
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function findOneById($id){
		$sql = $this->getDb()->prepare("select * from event where id = ?");
		$sql->bindValue(1,$id);
		$sql->execute();
		$event = $sql->fetch();

		if($event){
			return $this->buildDomainObject($event);
		}
	}

	/**
	 * @param $id_user
	 * @return array (events)
	 */
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

	/**
	 * @param Event $event
	 */
	public function save(Event $event){
		$eventData = array(
			'name' => $event->getName(),
			'description' => $event->getDescription(),
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

	/**
	 * @param $id
	 * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
	 */
	public function delete($id){
		$this->getDb()->delete('event',array('id' => $id));
	}


	/**
	 * @param $row containing the event data
	 * @return Event
	 */
	protected function buildDomainObject($row) {
		$event = new Event();
		$event->setId($row['id']);
		$event->setName($row['name']);
		$event->setDescription($row['description']);
		$event->setIdUsers($row['id_user']);
		return $event;
    }
}