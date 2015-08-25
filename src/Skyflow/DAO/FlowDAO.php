<?php

/**
 * DAO class for the Flow Domain Object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use skyflow\Domain\Flow;

/**
 * DAO class for the Flow Domain Object.
 */
class FlowDAO extends DAO {

    /**
     * Find a User's Flow by name.
     *
     * @param string $name   The Flow name.
     * @param string $idUser The id of the User who owns the Flow.
     * @return Flow|null The found Flow or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOne($name,$idUser){
        $sql = $this->getDb()->prepare("select * from flow where name = ? and id_user = ?");
        $sql->bindValue(1,$name);
        $sql->bindValue(2,$idUser);
        $sql->execute();
        $flow = $sql->fetch();

        if($flow){
            return $flow;
        }
    }

    /**
     * Find a Flow by its id.
     *
     * @param string $id The Flow id.
     * @return Flow|null The found Flow or null if none found.
     */
    public function findOneById($id){
        $sql = $this->getDb()->prepare("select * from flow where id = ?");
        $sql->bindValue(1,$id);
        $sql->execute();
        $flow = $sql->fetch();

        if($flow){
            return $this->buildDomainObject($flow);
        }
    }

    /**
     * Find all Flows owned by a User using the user's id.
     *
     * @param string $id_user The id of the User who owns the Flows to find.
     * @return Flow[] An array of found Flows. Empty array if none found.
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
     * Save a Flow.
     *
     * @param Flow $flow The Flow to save.
     */
    public function save(Flow $flow){
        $flowData = array(
            'name' => $flow->getName(),
            'class' => $flow->getClass(),
            'documentation' => $flow->getDocumentation(),
            'id_user' => $flow->getIdUser(),
        );

        if($flow->getId()) {
            $this->getDb()->update('flow',$flowData, array('id' => $flow->getId()));
        } else {
            $this->getDb()->insert('flow',$flowData);
            $id = $this->getDb()->lastInsertId();
            $flow->setId($id);
        }
    }

    /**
     * Delete a Flow.
     *
     * @param string $id The id of the Flow to delete.
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id){
        $this->getDb()->delete('flow',array('id' => $id));
    }

    /**
     * Creates a Flow object based on a DB row.
     *
     * @param array $row The DB row containing Flow data.
     * @return Flow
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