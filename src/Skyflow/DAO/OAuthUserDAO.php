<?php

/**
 * DAO Object for OAuth users.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\Domain\AbstractModel;
use Silex\Application;

/**
 * DAO Obejct for OAuth users.
 */
class OAuthUserDAO extends AbstractDAO
{
    /**
     * Use for the access at the security crypt/uncrypt
     * @var null|Application
     */
    protected $app = null;

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
        Application $app,
        $objectType = null,
        $domainObjectClass = 'skyflow\\Domain\\OAuthUser',
        $provider = null
    ) {
        $this->app = $app;
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
        $data[$prefix . 'client_secret'] = $this->app['skyflow.config']['security']['crypt']($model->getClientSecret(),$model->getId(),$this->app);
        $data[$prefix . 'access_token'] = $this->app['skyflow.config']['security']['crypt']($model->getAccessToken(),$model->getId(),$this->app);
        $data[$prefix . 'refresh_token'] = $this->app['skyflow.config']['security']['crypt']($model->getRefreshToken(),$model->getId(),$this->app);

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
        $user->setClientSecret($this->app['skyflow.config']['security']['uncrypt']($row[$prefix . 'client_secret']),$user->getId(),$this->app);
        $user->setAccessToken($this->app['skyflow.config']['security']['uncrypt']($row[$prefix . 'access_token']),$user->getId(),$this->app);
        $user->setRefreshToken($this->app['skyflow.config']['security']['nucrypt']($row[$prefix . 'refresh_token'],$user->getId(),$this->app));
        
        return $user;
    }
}
