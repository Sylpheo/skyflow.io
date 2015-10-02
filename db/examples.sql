--
-- Provision the database with the example flows
-- located on directory src/Skyflow/Flow/Examples
--
-- This have to be run after install.sql
--
-- Do NOT forget to setup the client id and client secret for each addon or your
-- flow won't work !
--

WITH variables as (
  SELECT id::integer as skyflowuser FROM users WHERE username = 'skyflow'
) INSERT INTO event (
  name,
  description,
  id_user
) VALUES (
  'Example_SalesforceSimpleSoqlQuery',
  'Salesforce simple SOQL query example flow event.',
  (SELECT skyflowuser FROM variables)
), (
  'Example_SalesforceSObjectsCrud',
  'Salesforce SObjects CRUD example flow event.',
  (SELECT skyflowuser FROM variables)
), (
  'Example_WaveSimpleSaqlQuery',
  'Wave simple SAQL query example flow event.',
  (SELECT skyflowuser FROM variables)
), (
  'Example_WaveDatasetsList',
  'Wave datasets list example flow event.',
  (SELECT skyflowuser FROM variables)
), (
  'Example_WaveExternalData',
  'Wave External Data example flow event.',
  (SELECT skyflowuser FROM variables)
);

WITH variables as (
  SELECT id::integer as skyflowuser FROM users WHERE username = 'skyflow'
) INSERT INTO flow (
  name,
  class,
  documentation,
  id_user
) VALUES (
  'Example Salesforce simple SOQL query',
  'Skyflow\Flow\Example\SalesforceSimpleSoqlQuery',
  '<h1>Example flow for Salesforce simple SOQL query</h1><p>This flow demonstrates usage of the query() method from the <em>salesforce.data</em> service to create a simple SOQL query from a query string and get the records result.</p>',
  (SELECT skyflowuser FROM variables)
), (
  'Example Salesforce SObjects CRUD',
  'Skyflow\Flow\Example\SalesforceSObjectsCrud',
  '<h1>Example flow for Salesforce SObjects CRUD operations</h1><p>This flow demonstrates usage of the methods from the <em>salesforce.data.sobjects</em> service to create, update and delete SObjects.</p>',
  (SELECT skyflowuser FROM variables)
), (
  'Example Wave simple SAQL query',
  'Skyflow\Flow\Example\WaveSimpleSaqlQuery',
  '<h1>Example flow for Wave simple SAQL query</h1><p>This flow demonstrates usage of the query() method from the <em>wave.data</em> service to create a simple SAQL query from a query string and get the result.</p>',
  (SELECT skyflowuser FROM variables)
), (
  'Example Wave datasets list',
  'Skyflow\Flow\Example\WaveDatasetsList',
  '<h1>Example flow that lists the Wave datasets</h1><p>This flow demonstrates usage of the datasets() method from the <em>wave.data</em> service to list the available Wave datasets.</p>',
  (SELECT skyflowuser FROM variables)
), (
  'Example Wave External Data',
  'Skyflow\Flow\Example\WaveExternalData',
  '<h1>Example flow for Wave External Data</h1><p>This flow demonstrates import of an External Data in Wave (aka Salesforce Analytics Cloud) using the <em>wave.externaldata</em> service.</p>',
  (SELECT skyflowuser FROM variables)
);

WITH variables as (
  SELECT id::integer as skyflowuser FROM users WHERE username = 'skyflow'
) INSERT INTO mapping (
  id_event,
  id_flow,
  id_user
) VALUES (
  (SELECT id FROM event WHERE name = 'Example_SalesforceSimpleSoqlQuery'),
  (SELECT id FROM flow WHERE name = 'Example Salesforce simple SOQL query'),
  (SELECT skyflowuser FROM variables)
), (
  (SELECT id FROM event WHERE name = 'Example_SalesforceSObjectsCrud'),
  (SELECT id FROM flow WHERE name = 'Example Salesforce SObjects CRUD'),
  (SELECT skyflowuser FROM variables)
), (
  (SELECT id FROM event WHERE name = 'Example_WaveSimpleSaqlQuery'),
  (SELECT id FROM flow WHERE name = 'Example Wave simple SAQL query'),
  (SELECT skyflowuser FROM variables)
), (
  (SELECT id FROM event WHERE name = 'Example_WaveDatasetsList'),
  (SELECT id FROM flow WHERE name = 'Example Wave datasets list'),
  (SELECT skyflowuser FROM variables)
), (
  (SELECT id FROM event WHERE name = 'Example_WaveExternalData'),
  (SELECT id FROM flow WHERE name = 'Example Wave External Data'),
  (SELECT skyflowuser FROM variables)
);
