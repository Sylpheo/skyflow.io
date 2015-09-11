<?php

/**
 * DAO class for the Wave Request domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\DAO;

use Skyflow\DAO\AbstractDAO;
use Skyflow\Domain\AbstractModel;

use Wave\Domain\WaveRequest;

/**
 * DAO class for the Wave Request domain object.
 */
class WaveRequestDAO extends AbstractDAO
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
     * {@inheritdoc}
     */
    public function getData(AbstractModel $domainObject)
    {
        return array (
            'request' => $waveRequest->getRequest(),
            'id_user' => $waveRequest->getUserId(),
        );
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
