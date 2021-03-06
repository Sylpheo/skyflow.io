<?php

/**
 * DAO Object for OAuth users.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\DAO;

use Doctrine\DBAL\Connection;

use Skyflow\Domain\AbstractModel;
use Skyflow\Security\EncryptionTrait;

/**
 * DAO Obejct for OAuth users.
 */
class OAuthUserDAO extends AbstractDAO
{
    use EncryptionTrait;

    /**
     * The Provider prefix.
     *
     * May it be "salesforce_" or "wave_" or "_exact_target", or others...
     *
     * @var string
     */
    private $providerPrefix;

    /**
     * Constructor
     *
     * @param Connection $db                The database connection object.
     * @param string     $objectType        The model object type handled by this DAO.
     * @param string     $domainObjectClass The domain object class instantiated
     *                                      by this DAO.
     * @param string     $provider          The name of the provider. It will be
     *                                      used as a field prefix. For example
     *                                      "Salesforce" will become prefix
     *                                      "salesforce_" in OAuth fields.
     */
    public function __construct(
        Connection $db,
        $objectType = null,
        $domainObjectClass = 'Skyflow\\Domain\\OAuthUser',
        $provider = null
    ) {
        parent::__construct($db, $objectType, $domainObjectClass);
        $this->providerPrefix = isset($provider) ? $this->normalize($provider) . '_' : '';
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
     * {@inheritdoc}
     */
    public function getData(AbstractModel $model)
    {
        $data = parent::getData($model);
        $prefix = $this->getProviderPrefix();

        $data[$prefix . 'client_id'] = $model->getClientId();
        $data[$prefix . 'client_secret'] = $this->getEncryption()->encrypt($model->getClientSecret(), $model->getId());
        $data[$prefix . 'access_token'] = $this->getEncryption()->encrypt($model->getAccessToken(), $model->getId());
        $data[$prefix . 'refresh_token'] = $this->getEncryption()->encrypt($model->getRefreshToken(), $model->getId());

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $user = parent::buildDomainObject($row);
        $prefix = $this->getProviderPrefix();

        $user->setClientId($row[$prefix . 'client_id']);
        $user->setClientSecret($this->getEncryption()->decrypt($row[$prefix . 'client_secret'], $row['id']));
        $user->setAccessToken($this->getEncryption()->decrypt($row[$prefix . 'access_token'], $row['id']));
        $user->setRefreshToken($this->getEncryption()->decrypt($row[$prefix . 'refresh_token'], $row['id']));
        return $user;
    }
}
