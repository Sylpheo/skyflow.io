<?php

/**
 * Abstract class for models that are owned by a user.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

use skyflow\Domain\AbstractModel;

/**
 * Abstract class for models that are owned by a user.
 *
 * This class is abstract because it has no own fields, only userId.
 */
abstract class AbstractUserOwnedModel extends AbstractModel
{
    /**
    * The id of the User who owns the Model.
    *
    * @var string
    */
    private $userId;

    /**
     * Get the id of the User who owns the Model.
     *
     * @return string The id of the User who owns the Model.
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the id of the User who Owns the Model.
     *
     * @param string $id The id of the User who owns the Model.
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
