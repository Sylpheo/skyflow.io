<?php

/**
 * Wave OAuth user controller.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Controller;

use Salesforce\Controller\SalesforceOAuthUserController;

/**
 * Wave OAuth user controller.
 */
class WaveOAuthUserController extends SalesforceOAuthUserController
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
            parent::credentialsAction();
        }

        return $this->getTwig()->render(
            'wave/setup/credentials-form.html.twig',
            array('credentialsForm' => $this->getCredentialsForm()->createView())
        );
    }
}
