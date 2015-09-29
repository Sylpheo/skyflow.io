<?php

/**
 * Salesforce Query service.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service\Data;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use Skyflow\Service\OAuthServiceInterface;
use Skyflow\Domain\OAuthUser;
use Skyflow\Service\RestOAuthAuthenticatedService;

/**
 * Salesforce Query service.
 */
class SalesforceQueryService extends RestOAuthAuthenticatedService
{
    /**
     * The SObject type to query from.
     *
     * @var string
     */
    private $from;

    /**
     * The query WHERE clause.
     *
     * @var string
     */
    private $where;

    /**
     * The fields to query.
     *
     * @var string[]
     */
    private $fields = array();

    /**
     * Associative array of the fields aliases.
     *
     * Key is the Salesforce field name e.g. "Example__c",
     * "Related__r.Example__c" or "Count(Id)".
     *
     * Value is the field alias e.g. "My super field alias".
     *
     * @var array
     */
    protected $aliases = array();

    /**
     * Associative array of the fields transforms callbacks.
     *
     * Key is the Salesforce field name e.g. "Example__c" or
     * "Related__r.Example__c" or "Count(Id)".
     *
     * Value is the field transform callback function :
     * function (&$value, $record, $records)
     *
     * 1st parameter &$value is the field current value passed by reference.
     * 2nd parameter $record is an array of all the fields of current record.
     * 3rd parameter $records is an array of all the records of current query.
     *
     * When declaring the callback you are not required to declare the 2nd and
     * 3rd parameter if you don't need them.
     *
     * IMPORTANT: the callback return value is ignored. As the $value parameter
     * is passed by reference you can change its value directly.
     *
     * function (&$value) {
     *     $value = 'hello';
     * }
     *
     * @var array
     */
    protected $transforms = array();

    /**
     * The query to execute.
     *
     * This is set by the static method SalesforceQueryService::query($query)
     * via protected function setQuery($query) and should not be set elsewhere.
     *
     * It must not be writable from the outside world so don't create a public
     * setters for the query attribute.
     *
     * It must be accessible from the protected method setQuery() and getQuery()
     * so it must be protected to allow overriding in subclasses.
     *
     * @var string
     */
    protected $query;

    /**
     * The records result.
     *
     * Is null until the query is processed.
     *
     * @var array
     */
    private $records;

    /**
     * Whether to include missing relations fields or not.
     *
     * When querying through relations e.g. "Related__r.Example__c" if there is
     * no Related__c associated for the record, the fields will be missing in
     * the records query result (attribute $records). By setting this attribute
     * to true, the missing relations fields will be included with the value of
     * NULL in the records associative array.
     *
     * @var boolean
     */
    private $includeMissingRelationsFields = true;

    /**
     * Whether to use headings or query fieldnames for the keys in the records
     * associative array.
     *
     * Setting this to true means that if an alias has been set for the field,
     * the alias will be used as a key in the records associative array. If there
     * is no alias, the query field name will be used.
     *
     * Setting this to false means that the query field name will always be used,
     * even if an alias has been set.
     *
     * @var boolean
     */
    private $useHeadings = true;

    /**
     * Whether this query instance is locked or not.
     *
     * A query instance is locked after the query has processed. A locked
     * instance cannot see its attribute change. But a locked instance may be
     * processed again to fetch latest records.
     *
     * @var boolean
     */
    private $isLocked = false;

    /**
     * Execute a query from query string and return the records.
     *
     * @param SalesforceQueryService $parentService The query parent service to use.
     * @param string                 $query         The query to execute.
     * @return array                 The records query result.
     */
    public static function query(SalesforceQueryService $parentService, $query)
    {
        $queryInstance = new SalesforceQueryService(
            $parentService,
            null,
            $parentService->getHttpClient(),
            $parentService->getUser(),
            $parentService->getAuthService()
        );

        $queryInstance->setQuery($query);
        $queryInstance->process();

        return $queryInstance->getRecords();
    }

    /**
     * Create a query.
     *
     * @param boolean $inherit Whether the new query must inherit its parent query.
     * @return SalesforceQueryService A new instance of the Salesforce Query Service
     *                                initialized for a new query.
     */
    public function create($inherit = false)
    {
        $query = new SalesforceQueryService(
            $this,
            null,
            $this->getHttpClient(),
            $this->getUser(),
            $this->getAuthService()
        );

        $query->init($inherit);

        return $query;
    }

    /**
     * Initialize the SalesforceQueryService query.
     *
     * Please leave this method protected. See WaveExternalDataService::init()
     * for explanations.
     *
     * @param boolean $inherit Whether the query must inherit its parent query.
     */
    protected function init($inherit)
    {
        if ($inherit) {
            $this->inherit();
        }
    }

    /**
     * Inherit query from parent service.
     */
    protected function inherit()
    {
        $this->setFrom($this->getParentService()->getFrom());
        $this->setWhere($this->getParentService()->getWhere());
        $this->setFields($this->getParentService()->getFields());
        $this->setAliases($this->getParentService()->getAliases());
        $this->setTransforms($this->getParentService()->getTransforms());

        // Parent service must be an instance of SalesforceQueryService or of
        // its subclasses.
        if (isset($this->getParentService()->query)) {
            $this->setQuery($this->getParentService->query);
        }
    }

    /**
     * Process the query and store the records.
     *
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function process()
    {
        $query = $this->getQuery();

        $response = $this->httpGet('', array('q' => $query));

        $records = $response->json()['records'];

        $records = $this->processRecords($records);
        $records = $this->applyTransforms($records);
        $records = $this->applyHeadings($records);

        $this->setRecords($records);
        $this->setIsLocked(true);

        return $this;
    }

    /**
     * Process the records from Salesforce query result.
     *
     * @param array $records The records from the Salesforce query result.
     * @return array The processed records.
     */
    protected function processRecords(array $records)
    {
        $processedRecords = array();

        // convert the multi-level depth Salesforce query response to a flat array
        foreach ($records as $record) {
            array_push($processedRecords, $this->processRecord($record));
        }

        return $processedRecords;
    }

    /**
     * Process a record from Salesforce query result.
     *
     * This method ignores the special "attributes" field of the record.
     *
     * This method converts the multi-level depth Salesforce query response
     * to a flat array.
     *
     * @param  array $record   The record from the Salesforce query result.
     * @param  array $prefixes The different relations names encountered.
     * @return array The processed record.
     */
    protected function processRecord(array $record, $prefix = '')
    {
        $values = array();

        unset($record['attributes']);

        foreach ($record as $field => $value) {
            if (substr($field, -3) === '__r') {
                // encountered a relation
                // relations may be null
                if (!is_null($value)) {
                    $relValues = $this->processRecord($value, $prefix . $field . '.');
                    $values = array_merge($values, $relValues);
                } else {
                    // Generate missing relations fields.
                    if ($this->getIncludeMissingRelationsFields() === true) {
                        $relationName = $prefix . $field;

                        $missingFields = array_filter(
                            $this->getFields(),
                            function ($field) use ($relationName) {
                                return strpos($field, $relationName, 0) === 0;
                            }
                        );

                        foreach ($missingFields as $field) {
                            // null default value for missing relations fields
                            $values[$field] = null;
                        }
                    }
                }
            } else {
                $values[$prefix . $field] = $value;
            }
        }

        return $values;
    }

    /**
     * Apply the transform callbacks to the records.
     *
     * @param  array $records The processed records.
     * @return array The transformed records
     */
    protected function applyTransforms(array $records)
    {
        foreach ($records as &$record) {
            foreach ($record as $field => &$value) {
                if (array_key_exists($field, $this->transforms)) {
                    $transform = $this->transforms[$field];

                    if (is_object($transform) && ($transform instanceof Closure)) {
                        $transform($value, $record, $records);
                    } else {
                        call_user_func_array(
                            $this->transforms[$field],
                            array(&$value, $record, $records)
                        );
                    }
                }
            }
            unset($value);
        }
        unset($record);

        return $records;
    }

    /**
     * Apply the headings to the records associative array.
     *
     * @return array The records with updated headings.
     */
    protected function applyHeadings(array $records)
    {
        if ($this->getUseHeadings() === false) {
            return $records;
        } else {
            $remappedRecords = array();
            $aliases = $this->getAliases();

            foreach ($records as $record) {
                $remappedRecord = array();

                foreach ($record as $key => $value) {
                    if (array_key_exists($key, $aliases)) {
                        $key = $aliases[$key];
                    }

                    $remappedRecord[$key] = $value;
                }

                array_push($remappedRecords, $remappedRecord);
            }

            return $remappedRecords;
        }
    }

    /**
     * Get the query headings.
     *
     * If an alias is defined for a field, return the alias, else return the
     * Salesforce field name.
     *
     * @return array The query headings.
     */
    public function getHeadings()
    {
        $headings = array();

        foreach ($this->fields as $field) {
            if (array_key_exists($field, $this->aliases)) {
                array_push($headings, $this->aliases[$field]);
            } else {
                array_push($field);
            }
        }

        return $headings;
    }


    /**
     * Set the query FROM clause.
     *
     * @param string $from The value for the query FROM clause.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function setFrom($from)
    {
        $this->checkIsLocked();

        $this->from = $from;
        return $this;
    }

    /**
     * Get the query FROM clause.
     *
     * @return string The query FROM clause.
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the query WHERE clause.
     *
     * @param string $where The value for the query WHERE clause.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function setWhere($where)
    {
        $this->checkIsLocked();

        $this->where = $where;
        return $this;
    }

    /**
     * Get the query WHERE clause.
     *
     * @return string The query WHERE clause.
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * Set the query fields.
     *
     * @param array $fields The query fields.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function setFields(array $fields)
    {
        $this->checkIsLocked();

        $this->fields = $fields;
        return $this;
    }

    /**
     * Get the query fields.
     *
     * @return array The query fields.
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add a field to the query.
     *
     * This is the main entry-point to adding fields. It allows to set the field
     * optional alias and optional transform callback.
     *
     * @param string $name      The required Salesforce field name e.g.
     *                          "Example__c" or "Related__r.Example__c" or
     *                          "Count(Id)".
     * @param string $alias     The optional field alias e.g. "My super alias".
     * @param mixed  $transform The optional transform callback.
     *                          See SalesforceQueryService::transforms attribute.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function addField($name, $alias = null, $transform = null)
    {
        $this->checkIsLocked();

        $fields = $this->getFields();
        array_push($fields, $name);
        $this->setFields($fields);

        if ($alias !== null) {
            $this->addAlias($name, $alias);
        }

        if ($transform !== null) {
            $this->addTransform($name, $transform);
        }

        return $this;
    }

    /**
     * Set the query fields aliases.
     *
     * @param array $aliases The field aliases.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function setAliases(array $aliases)
    {
        $this->checkIsLocked();

        $this->aliases = $aliases;
        return $this;
    }

    /**
     * Get the query fields aliases.
     *
     * @return array The query fields aliases.
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Add a query field alias.
     *
     * @param string $field The field name.
     * @param string $alias The field alias.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function addAlias($name, $alias)
    {
        $this->checkIsLocked();

        $this->aliases[$name] = $alias;
        return $this;
    }

    /**
     * Set the query fields transforms callbacks.
     *
     * @param array $transforms The fields transforms callbacks.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function setTransforms(array $transforms)
    {
        $this->checkIsLocked();

        $this->transforms = $transforms;
        return $this;
    }

    /**
     * Get the query fields transforms callbacks.
     *
     * @return array The query fields transforms callbacks.
     */
    public function getTransforms()
    {
        return $this->transforms;
    }

    /**
     * Add a query field transform callback.
     *
     * @param string $name     The field name.
     * @param mixed  $callback The field transform callback.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function addTransform($name, $callback)
    {
        $this->checkIsLocked();

        $this->transforms[$name] = $callback;
        return $this;
    }

    /**
     * Set the query that this instance will execute.
     *
     * Do NOT change it to public. This method must be called only from the
     * static method SalesforceQueryService::query($query).
     *
     * If called elsewhere it may cause unconsistency on the state of the query.
     *
     * @param string $query The query that this instance will execute.
     */
    protected function setQuery($query)
    {
        $this->checkIsLocked();

        $this->query = $query;
    }

    /**
     * Get the query string for this SalesforceQueryService instance.
     *
     * The query is dynamically built when the $query attribute is unset.
     * The query attribute must be defined only via a call to static method
     * SalesforceQueryService::query($query).
     *
     * @return string The query string for this SalesforceQueryService instance.
     */
    public function getQuery()
    {
        if (isset($this->query)) {
            // call from static method query($query)
            $query = $this->query;
        } else {
            if (empty($this->getFrom())) {
                throw new \Exception(
                    'Incomplete query : missing FROM clause. '
                    . 'Did you forget to use setFrom() ?'
                );
            }

            if (empty($this->getFields())) {
                throw new \Exception(
                    'Incomplete query : there must be at least one field to query. '
                    . 'Did you forget to use addField() ?'
                );
            }

            $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM ' . $this->from;

            if (!empty($this->where)) {
                $query .= ' WHERE ' . $this->where;
            }
        }

        return $query;
    }

    /**
     * Set the records query result.
     *
     * Leave this method protected. It is nonsense for outside world to define
     * the records result.
     *
     * @param array The records query result.
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    protected function setRecords(array $records)
    {
        $this->records = $records;
        return $this;
    }

    /**
     * Get the records query result.
     *
     * @return array The records query result.
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set whether the missing relations fields are included or not in the
     * records associative array.
     *
     * This method is not intended to be used by the outside world.
     * Instead, use includeMissingRelationsFields() and
     * excludeMissingRelationsFields().
     *
     * @param boolean $includeMissingRelationsFields Whether to include or not.
     */
    protected function setIncludeMissingRelationsFields(
        $includeMissingRelationsFields
    ) {
        $this->includeMissingRelationsFields = $includeMissingRelationsFields;
    }

    /**
     * Get whether the missing relations fields are included or not.
     *
     * @return boolean Whether the missing relations fields are included or not.
     */
    public function getIncludeMissingRelationsFields()
    {
        return $this->includeMissingRelationsFields;
    }

    /**
     * Include the missing relations fields in the records query result.
     *
     * We must not be able to include missing relations fields once the query has
     * been processed because this would change the value returned by public
     * method getIncludeMissingRelationsFields(). That would cause ambiguity with
     * the content of the records query result.
     *
     * The opposite method is excludeMissingRelationsFields().
     *
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function includeMissingRelationsFields()
    {
        $this->checkIsLocked();

        $this->setIncludeMissingRelationsFields(true);
        return $this;
    }

    /**
     * Exclude the missing relations fields from the records query result.
     *
     * We must not be able to exclude missing relations fields once the query has
     * been processed because this would change the value returned by public
     * method getIncludeMissingRelationsFields(). That would cause ambiguity with
     * the content of the records query result.
     *
     * The opposite method is includeMissingRelationsFields().
     *
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function excludeMissingRelationsFields()
    {
        $this->checkIsLocked();

        $this->setIncludeMissingRelationsFields(false);
        return $this;
    }

    /**
     * Get whether the headings are used as key for the records associative array.
     *
     * @return boolean Whether the headings are used or not.
     */
    public function getUseHeadings()
    {
        return $this->useHeadings;
    }

    /**
     * Set whether the headings are used as key for the records associative array.
     *
     * This method is not intended to be used by the outside world.
     * Instead, use useHeadings() and useFieldnames().
     *
     * @param boolean $useHeadings Whether the headings are used or not.
     */
    protected function setUseHeadings($useHeadings)
    {
        $this->useHeadings = $useHeadings;
    }

    /**
     * Use the headings as key for the records associative array.
     *
     * We must not be able to change the useHeadings option once the query has
     * been processed because this would change the value returned by public
     * method getUseHeadings(). That would cause ambiguity with the content of
     * the records query result.
     *
     * The opposite method is useFieldnames().
     *
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function useHeadings()
    {
        $this->checkIsLocked();

        $this->setUseHeadings(true);
        return $this;
    }

    /**
     * Use only the query field names as key for the records associative array.
     *
     * We must not be able to change the useHeadings option once the query has
     * been processed because this would change the value returned by public
     * method getUseHeadings(). That would cause ambiguity with the content of
     * the records query result.
     *
     * The opposite method is useHeadings().
     *
     * @return SalesforceQueryService The current instance of SalesforceQueryService
     *                                to enable methods chaining.
     */
    public function useFieldnames()
    {
        $this->checkIsLocked();

        $this->setUseHeadings(false);
        return $this;
    }

    /**
     * Set whether the query instance is locked or not.
     *
     * @param boolean $isLocked Whether the instance is locked or not.
     */
    protected function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     * Get whether the query instance is locked or not.
     *
     * @return boolean Whether the instance is locked or not.
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Check whether the query instance is locked.
     *
     * @throws \Exception If the instance is locked.
     */
    protected function checkIsLocked()
    {
        if ($this->getIsLocked()) {
            throw new \Exception(
                'Query is locked. Use create() to create a new query.'
            );
        }
    }
}
