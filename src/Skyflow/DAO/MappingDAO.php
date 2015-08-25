<?php

/**
 * DAO class for the Mapping domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\DAO;

use Skyflow\Domain\Mapping;

/**
 * DAO class for the Mapping domain object.
 */
class MappingDAO extends DAO {

    /**
     * @var \Skyflow\DAO\EventDAO The related Event DAO object.
     */
    private $eventDAO;

    /**
     * @var \Skyflow\DAO\FlowDAO The related Flow DAO object.
     */
    private $flowDAO;

    /**
     * Set the related Event DAO object.
     *
     * @param EventDAO $eventDAO The related Event DAO object.
     */
    public function setEventDAO(EventDAO $eventDAO){
        $this->eventDAO = $eventDAO;
    }

    /**
     * Set the related Flow DAO object.
     *
     * @param FlowDAO $flowDAO The related Flow DAO object.
     */
    public function setFlowDAO(FlowDAO $flowDAO){
        $this->flowDAO = $flowDAO;
    }

    /**
     * Find a Mapping by its id.
     *
     * @param string $id The Mapping id.
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOne($id) {
        $sql = $this->getDb()->prepare("select * from mapping where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $mapping = $sql->fetch();

        if($mapping){
            return $association;
        }
    }

    /**
     * Find all Mappings owned by a User using the user's id.
     *
     * @param $id_user The id of the User who owns the Mappings to find.
     * @return Mapping[] An array of found Mappings. Empty array if none found.
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

    /**
     * Save a Mapping.
     *
     * @param Mapping $mapping The Mapping to save.
     */
    public function save(Mapping $mapping){
        $mappingData = array(
            'id_event' => $mapping->getEvent()->getId(),
            'id_flow' => $mapping->getFlow()->getId(),
            'id_user' => $mapping->getIdUser(),
        );

        if($mapping->getId()) {
            $this->getDb()->update('mapping',$mappingData, array('id' => $mapping->getId()));
        } else {
            $this->getDb()->insert('mapping',$mappingData);
            $id = $this->getDb()->lastInsertId();
            $mapping->setId($id);
        }
    }

    /**
     * Find a Mapping from it's User id and related Event id.
     *
     * @param string $id_event The id of the related Event.
     * @param string $id_user  The id of the User who owns the Mapping.
     * @return Mapping|null The found Mapping or null if none found.
     */
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
     * Delete a Mapping.
     *
     * @param string $id The id of the Mapping to delete.
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id){
        $this->getDb()->delete('mapping',array('id' => $id));
    }


    /**
     * Creates a Mapping object based on a DB row.
     *
     * @param array $row The DB row containing Mapping data.
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