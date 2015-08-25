<?php

/**
 * DAO class for the Event Domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\DAO;

use Skyflow\Domain\Event;

class EventDAO extends DAO {

    /**
     * Find a User's Event by name.
     *
     * @param string $name   The Event name.
     * @param string $idUser The id of the User who owns the Event.
     * @return Event|null The found Event or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOne($name, $idUser) {
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
     * Find an Event by its id.
     *
     * @param $id The Event id.
     * @return Event|null The found Event or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOneById($id) {
        $sql = $this->getDb()->prepare("select * from event where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $event = $sql->fetch();

        if ($event) {
            return $this->buildDomainObject($event);
        }
    }

    /**
     * Find all Events owned by a User using the user's id.
     *
     * @param $id_user The id of the User who owns the Events to find.
     * @return Event[] An array of found Events. Empty array if none found.
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
     * Save an Event.
     *
     * @param Event $event The Event to save.
     */
    public function save(Event $event){
        $eventData = array(
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'id_user' => $event->getIdUsers(),
        );

        if($event->getId()) {
            $this->getDb()->update('event',$eventData, array('id' => $event->getId()));
        } else {
            $this->getDb()->insert('event',$eventData);
            $id = $this->getDb()->lastInsertId();
            $event->setId($id);
        }
    }

    /**
     * Delete an Event.
     *
     * @param string $id The id of the Event to delete.
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id){
        $this->getDb()->delete('event',array('id' => $id));
    }

    /**
     * Creates a Event object based on a DB row.
     *
     * @param array $row The DB row containing Event data.
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