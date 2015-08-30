<?php

/**
 * DAO Object for OAuth users.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\Domain\AbstractModel;

/**
 * DAO Obejct for OAuth users.
 */
class OAuthUserDAO extends AbstractDAO
{
    /**
     * The Provider prefix.
     *
     * May it be "salesforce_" or "wave_" or "_exact_target", or others...
     *
     * @var string
     */
    private $providerPrefix;

    /**
     * The class to instantiate during buildDomainObject.
     *
     * @var string
     */
    private $domainObjectClass;

    /**
     * Constructor
     *
     * @param Connection $db         The database connection object.
     * @param string     $objectType The model object type handled by this DAO.
     * @param string     $provider   The name of the provider. It will be used as
     *                               a field prefix. For example "Salesforce" will
     *                               become prefix "salesforce_" in OAuth fields.
     */
    public function __construct(
        Connection $db,
        $objectType = null,
        $provider = null,
        $domainObjectClass = 'skyflow\\Domain\\OAuthUser'
    ) {
        parent::__construct($db, $objectType);
        $this->providerPrefix = isset($provider) ? $this->normalize($provider) . '_' : null;
        $this->domainObjectClass = $domainObjectClass;
    }

    /**
     * Get the provider prefix used by the DAO when storing user OAuth data to db.
     *
     * This getter is public because provider prefix is a string so it is passed
     * by value : no risk to have provider prefix properties changed from the outside.
     *
     * @return string The provider prefix.
     */
    public function getProviderPrefix()
    {
        return $this->providerPrefix;
    }

    /**
     * Get the domain object class populated from db by this DAO.
     *
     * This getter is public because domain object class is a string so it is
     * passed by value : no risk to have provider prefix properties changed from
     * the outside.
     *
     * @return string The domain object class populated by this DAO.
     */
    public function getDomainObjectClass()
    {
        return $this->domainObjectClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(AbstractModel $domainObject)
    {
        return array(
            $this->getProviderPrefix() . 'client_id' => $domainObject->getClientId(),
            $this->getProviderPrefix() . 'client_secret' => $domainObject->getClientSecret(),
            $this->getProviderPrefix() . 'access_token' => $domainObject->getAccessToken(),
            $this->getProviderPrefix() . 'refresh_token' => $domainObject->getRefreshToken()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $user = new $this->domainObjectClass();
        $user->setId($row['id']);
        $user->setClientId($row[$this->getProviderPrefix() . 'client_id']);
        $user->setClientSecret($row[$this->getProviderPrefix() . 'client_secret']);
        $user->setAccessToken($row[$this->getProviderPrefix() . 'access_token']);
        $user->setRefreshToken($row[$this->getProviderPrefix() . 'refresh_token']);
        
        return $user;
    }
}
