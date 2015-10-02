--
-- Provision the database with the example flows
-- located on directory src/Skyflow/Flow/Examples
--
-- This have to be run after install.sql
--
-- Do NOT forget to setup the client id and client secret for each addon or your
-- flow won't work !
--

INSERT INTO event (
  id,
  name,
  description,
  id_user
) VALUES (
  1,
  'Example_SalesforceSimpleSoqlQuery',
  'Salesforce simple SOQL query example flow event.',
  1
), (
  2,
  'Example_SalesforceSObjectsCrud',
  'Salesforce SObjects CRUD example flow event.',
  1
), (
  3,
  'Example_WaveSimpleSaqlQuery',
  'Wave simple SAQL query example flow event.',
  1
), (
  4,
  'Example_WaveDatasetsList',
  'Wave datasets list example flow event.',
  1
), (
  5,
  'Example_WaveExternalData',
  'Wave External Data example flow event.',
  1
);

ALTER SEQUENCE event_id_seq START WITH 6;

INSERT INTO flow (
  id,
  name,
  class,
  documentation,
  id_user
) VALUES (
  1,
  'Example Salesforce simple SOQL query',
  'Skyflow\Flow\Example\SalesforceSimpleSoqlQuery',
  '<h1>Example flow for Salesforce simple SOQL query</h1><p>This flow demonstrates usage of the query() method from the <em>salesforce.data</em> service to create a simple SOQL query from a query string and get the records result.</p>',
  1
), (
  2,
  'Example Salesforce SObjects CRUD',
  'Skyflow\Flow\Example\SalesforceSObjectsCrud',
  '<h1>Example flow for Salesforce SObjects CRUD operations</h1><p>This flow demonstrates usage of the methods from the <em>salesforce.data.sobjects</em> service to create, update and delete SObjects.</p>',
  1
), (
  3,
  'Example Wave simple SAQL query',
  'Skyflow\Flow\Example\WaveSimpleSaqlQuery',
  '<h1>Example flow for Wave simple SAQL query</h1><p>This flow demonstrates usage of the query() method from the <em>wave.data</em> service to create a simple SAQL query from a query string and get the result.</p>',
  1
), (
  4,
  'Example Wave datasets list',
  'Skyflow\Flow\Example\WaveDatasetsList',
  '<h1>Example flow that lists the Wave datasets</h1><p>This flow demonstrates usage of the datasets() method from the <em>wave.data</em> service to list the available Wave datasets.</p>',
  1
), (
  5,
  'Example Wave External Data',
  'Skyflow\Flow\Example\WaveExternalData',
  '<h1>Example flow for Wave External Data</h1><p>This flow demonstrates import of an External Data in Wave (aka Salesforce Analytics Cloud) using the <em>wave.externaldata</em> service.</p>',
  1
);

ALTER SEQUENCE flow_id_seq START WITH 6;

INSERT INTO mapping (
  id,
  id_event,
  id_flow,
  id_user
) VALUES
  (1, 1, 1, 1),
  (2, 2, 2, 1),
  (3, 3, 3, 1),
  (4, 4, 4, 1),
  (5, 5, 5, 1);

ALTER SEQUENCE mapping_id_seq START WITH 6;