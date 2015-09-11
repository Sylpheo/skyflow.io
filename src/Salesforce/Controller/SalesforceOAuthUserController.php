<?php

/**
 * Salesforce OAuth user controller.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Controller;

use Skyflow\Controller\OAuthUserController;

class SalesforceOAuthUserController extends OAuthUserController
{
    /**
     * {@inheritdoc}
     */
    public function credentialsAction()
    {
        if (!$this->getCredentialsForm()->isSubmitted()) {
            $this->getCredentialsForm()->handleRequest($this->getRequest());
        }

        if ($this->getCredentialsForm()->isSubmitted()
            && $this->getCredentialsForm()->isValid()
        ) {
            $data = $this->getCredentialsForm()->getData();

            if (empty($this->getUser()->getAccessToken())
                || (
                    $this->getUser()->getClientId() !== $data['client_id']
                    || $this->getUser()->getClientSecret() !== $data['client_secret']
                    || $this->getUser()->getIsSandbox() !== $data['is_sandbox']
                )
            ) {
                $this->getUser()->setClientId($data['client_id']);
                $this->getUser()->setClientSecret($data['client_secret']);
                $this->getUser()->setIsSandbox($data['is_sandbox']);
                $this->getUserDAO()->save($this->getUser());

                $this->getAuthService()->authenticate();
            }
        }

        return $this->getTwig()->render(
            'salesforce/setup/credentials-form.html.twig',
            array('credentialsForm' => $this->getCredentialsForm()->createView())
        );
    }
}
