<?php

/**
 * DAO class for the Event Domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use skyflow\Domain\Event;

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
     * {@inheritdoc}
     */
    public function getData(Event $event)
    {
        return array(
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'id_user' => $event->getUserId(),
        );
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