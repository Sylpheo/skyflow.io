<?php

/**
 * DAO class for the Wave Request domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\DAO;

use skyflow\DAO\DAO;

use Wave\Domain\WaveRequest;

/**
 * DAO class for the Wave Request domain object.
 */
class WaveRequestDAO extends DAO
{
    /**
     * Find all Wave requests owned by a User from the User's id.
     *
     * @param integer $userId The User id.
     * @return WaveRequest[] An array of found WaveRequest. Empty array if none found.
     */
    public function findAllByUser($userId)
    {
        $sql = "select * from wave_request where id_user =? limit 10";
        $request = $this->getDb()->fetchAll($sql, array($userId));

        $requests = array();
        foreach ($request as $row) {
            $requestId = $row['id'];
            $requests[$requestId] = $this->buildDomainObject($row);
        }

        return $requests;
    }

    /**
     * Find a Wave request domain object from the request string.
     *
     * @param string $request The request string.
     * @param integer $userId The id of the User who owns the WaveRequest.
     * @return WaveRequest|null The found WaveRequest or null if none found.
     */
    public function findByRequest($request, $userId)
    {
        $sql = $this->getDb()->prepare("select * from wave_request where request = ? and id_user =?");
        $sql->bindValue(1, $request);
        $sql->bindValue(2, $userId);
        $sql->execute();
        $result = $sql->fetch();

        if ($result) {
            return $this->buildDomainObject($result);
        }
    }

    /**
     * Find a Wave request by its id.
     *
     * @param integer $id The Wave request id.
     * @return WaveRequest|null The found Wave request or null if none found.
     */
    public function findById($id)
    {
        $sql = $this->getDb()->prepare("select * from wave_request where id = ?");
        $sql->bindValue(1, $id);
        $sql->execute();
        $request = $sql->fetch();

        if ($request){
            return $this->buildDomainObject($request);
        }
    }

    /**
     * Save a Wave request domain object.
     *
     * @param WaveRequest $waveRequest The Wave request domain object to save.
     */
    public function save(WaveRequest $waveRequest)
    {
        $waveRequestData = array (
            'request' => $waveRequest->getRequest(),
            'id_user' => $waveRequest->getUserId(),
        );

        if ($waveRequest->getId()) {
            $this->getDb()->update('wave_request', $waveRequestData, array('id' => $waveRequest->getId()));
        } else {
            $this->getDb()->insert('wave_request', $waveRequestData);
            $id = $this->getDb()->lastInsertId();
            $waveRequest->setId($id);
        }
    }

    /**
     * Creates a WaveRequest object based on a DB row.
     *
     * @param array $row The DB row containing WaveRequest data.
     * @return WaveRequest
     */
    protected function buildDomainObject($row)
    {
        $waveRequest = new WaveRequest();
        $waveRequest->setId($row['id']);
        $waveRequest->setRequest($row['request']);
        $waveRequest->setUserId($row['id_user']);

        return $waveRequest;
    }
}
