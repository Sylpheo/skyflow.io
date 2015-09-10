<?php

/**
 * Salesforce SObjects service.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service\Data;

use skyflow\Service\RestOAuthAuthenticatedService;

/**
 * Salesforce SObject service.
 */
class SalesforceSObjectsService extends RestOAuthAuthenticatedService
{
    /**
     * Create an SObject.
     *
     * @param string $sobject The SObject name to create.
     * @param array  $fields  An associative array of sobject fields
     *                        with key as field name and value as field value.
     * @return string Response as string encoded in JSON format.
     */
    public function create($sobject, array $fields)
    {
        $response = $this->httpPost($sobject, $fields);
        return $response->json()['id'];
    }

    /**
     * Update an SObject.
     *
     * @param string $sobject The SObject name to update.
     * @param string $id      The SObject id to update.
     * @param array  $fields  An associative array of sobject fields
     *                        with key as field name and value as field value.
     * @return string Response as string encoded in JSON format.
     */
    public function update($sobject, $id, array $fields)
    {
        $response = $this->httpPatch($sobject . '/' . $id, $fields);
        return $response->json();
    }

    /**
     * Delete an SObject.
     *
     * @param string $sobject The SObject name.
     * @param string $id      The SObject id to delete.
     */
    public function delete($sobject, $id)
    {
        $response = $this->httpDelete($sobject . '/' . $id);
        return $response->json();
    }
}
