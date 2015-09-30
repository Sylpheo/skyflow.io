# Skyflow.io

Cloud ETL for Heroku [http://sylpheo.github.io/skyflow.io/](http://sylpheo.github.io/skyflow.io/)

## Deploying to Heroku using the Heroku Button

You can deploy your own version of Skyflow in seconds using the Heroku button below:

<a href="https://heroku.com/deploy?template=https://github.com/Sylpheo/skyflow.io">
  <img src="https://www.herokucdn.com/deploy/button.png" alt="Deploy">
</a>

Please note your heroku application name, it will be referred as *your-app-name* in the additional steps below :

1. Install the heroku toolbelt from [https://toolbelt.heroku.com/](https://toolbelt.heroku.com/).
2. Download *install.sql* from <a href="https://raw.githubusercontent.com/Sylpheo/skyflow.io/master/db/install.sql" download>https://raw.githubusercontent.com/Sylpheo/skyflow.io/master/db/install.sql</a> and provision the database :

	~~~
	heroku login
	heroku pg:psql -a your-app-name < install.sql
	~~~

3. Open the application in a browser (or go to **https://your-app-name.herokuapp.com/** where *your-app-name* is your heroku application name).

	~~~
	heroku open -a your-app-name
	~~~

4. Login using the default skyflow admin user

	~~~
	username: skyflow
	password: skyflow
	~~~

## Setup

In order to communicate with the different third-party platforms (such as Salesforce and Wave) you will need to setup the corresponding addons and provide your connected applications client id and client secret. You need to do this once.

### Setup your connected applications

#### Salesforce

Needed OAuth Scopes

* Access and manage your data (api)
* Perform requests on your behalf at any time (refresh\_token, offline\_access)

Callback URL

* https://your-app-name.herokuapp.com/salesforce/auth/callback

#### Wave

Needed OAuth Scopes

* Access and manage your data (api)
* Perform requests on your behalf at any time (refresh\_token, offline\_access)
* Access and manage your Wave data (wave\_api)

Callback URL

* https://your-app-name.herokuapp.com/wave/auth/callback

### Setup the Skyflow addons

Copy-paste your applications "Client ID" (also known as "Consumer Key") and Client Secret (also known as "Consumer Secret") to the corresponding addons setup forms. For Salesforce-related addons, don't forget to check the sandbox checkbox if your Salesforce organization is a sandbox.

You will be asked to authenticate using your credentials for the remote application. Login to the remote application and you will be redirected back to Skyflow. The addon is now ready to be used.

## Create your first flow

* Declare your flow using the Skyflow web interface.
* Create your flow class in the *src/Skyflow/Flow* directory. Example flow class :

~~~php
namespace Skyflow\Flow;

use Skyflow\Flow\AbstractFlow;

/**
 * A flow must extend the class Skyflow\Flow\AbstractFlow.
 */
class ExampleFlow extends AbstractFlow
{
    /**
     * The code that will be executed when receiving an HTTP POST request.
     *
     * @param string $requestJson The JSON request.
     * @return mixed The content you decide to return.
     */
    public function event($requestJson)
    {
        return $this->run();
    }

    /**
     * The code that will be executed via the "Run" button on the Skyflow web
     * interface or via Heroku Scheduler.
     *
     * This method can NOT have parameters.
     *
     * @return mixed The content you decide to return.
     */
    public function run()
    {
        // Your flow code...
    }
}
~~~


## Use flow services

### Salesforce

1. Send a SOQL query to Salesforce.

	- Simple version : using a query string.

		Usage of `query()` from *salesforce.data* :

		~~~php
		$records = $this->get('salesforce.data')->query('SELECT FirstName, LastName FROM Contact');
		var_dump($records);
		~~~

		Result

		~~~php
		array(2) {
			[0]=>
			array(2) {
				["FirstName"]=>
				string(4) "John"
				["LastName"]=>
				string(3) "Doe"
			}
			[1]=>
			array(2) {
				["FirstName"]=>
				string(4) "Jane"
				["LastName"]=>
				string(3) "Doe"
			}
		}
		~~~

	- Advanced version : building the query.

		Usage of `create()`, `setFrom()`, `addField()`, `setWhere()`, `process()`, `getRecords()` from *salesforce.data.query* :

		~~~php
		$query = $this->get('salesforce.data.query')->create()
		->setFrom('Contact')
		->addField('FirstName', 'Contact First Name', function (&$value, $record) {
			if ($value === 'Jane' && $record['LastName'] === 'Doe') {
				$value = 'Sarah';
			}
		})
		->addField('LastName', 'Contact Last Name', function (&$value, $record) {
			if ($record['FirstName'] === 'Sarah' && $value === 'Doe') {
				$value = 'Connor';
			}
		})
		->setWhere("LeadSource = 'Series'")
		->process();

		$records = $query->getRecords();
		var_dump($records);
		~~~

		Result

		~~~php
		array(2) {
			[0]=>
			array(2) {
				["Contact First Name"]=>
				string(4) "John"
				["Contact Last Name"]=>
				string(3) "Doe"
			}
			[1]=>
			array(2) {
				["Contact First Name"]=>
				string(4) "Sarah"
				["Contact Last Name"]=>
				string(3) "Connor"
			}
		}
		~~~

		#### Query service inheritance

		Usage of `create($inherit = false)` from *salesforce.data.query* :

		~~~php
		// $query is the query above with John Doe and Sarah Connor.
		// set $inherit = true, the default value for $inherit is false
		// and add the field "LeadSource" without alias nor transform callback
		$newQuery = $query->create(true)
		->addField('LeadSource')
		->process();

		var_dump($newQuery->getRecords());
		~~~

		Result (note that the FirstName, LastName, aliases and transforms have been inherited)

		~~~php
		array(2) {
			[0]=>
			array(3) {
				["Contact First Name"]=>
				string(4) "John"
				["Contact Last Name"]=>
				string(3) "Doe"
				["LeadSource"]=>
				string(6) "Series"
			}
			[1]=>
			array(3) {
				["Contact First Name"]=>
				string(4) "Sarah"
				["Contact Last Name"]=>
				string(3) "Connor",
				["LeadSource"]=>
				string(6) "Series"
			}
		}
		~~~

		#### Aliases and transforms callbacks

		The `addField($name, $alias = null, $transform = null)` method accepts three arguments :

		- $alias is the field alias (optional, set to `null` for no alias)
		- $transform is the transform callback (optional)

		#### Aliases

		Usage of `useAliases()`, `useFieldnames()`, `setFields()`, `setAliases()`, `addAlias()`, `getHeadings()` from *salesforce.data.query* :

		~~~php
		// useAliases() is optional. This is the default when building the query.
		$query = $this->get('salesforce.data.query')->create()
		->useAliases()
		->setFields(array(
			'FirstName',
			'LastName',
			'LeadSource'
		))
		->setAliases(array(
			'FirstName' => 'Contact First Name',
			'LastName' => 'Contact Last Name'
		))
		->addAlias('LeadSource', 'Contact Lead Source')
		->process();

		var_dump($query->getRecords());
		var_dump($query->getHeadings());

		// inherit the above query
		// use the fieldnames as written in the query
		$query = $query->create(true)
		->useFieldnames()
		->process();

		var_dump($query->getRecords());
		var_dump($query->getHeadings());
		~~~

		Result

		~~~php
		// ----- with useAliases() -----

		// getRecords()
		array(2) {
			[0]=>
			array(3) {
				["Contact First Name"]=>
				string(4) "John"
				["Contact Last Name"]=>
				string(3) "Doe"
				["Contact Lead Source"]=>
				string(6) "Series"
			}
			[1]=>
			array(3) {
				["Contact First Name"]=>
				string(4) "Jane"
				["Contact Last Name"]=>
				string(3) "Doe"
				["Contact Lead Source"]=>
				string(6) "Series"
			}
		}

		// getHeadings()
		array(3) {
			string(18) "Contact First Name"
			string(17) "Contact Last Name"
			string(19) "Contact Lead Source"
		}

		// ----- with useFieldnames() -----

		// getRecords()
		array(2) {
			[0]=>
			array(3) {
				["FirstName"]=>
				string(4) "John"
				["LastName"]=>
				string(3) "Doe"
				["LeadSource"]=>
				string(6) "Series"
			}
			[1]=>
			array(3) {
				["FirstName"]=>
				string(4) "Jane"
				["LastName"]=>
				string(3) "Doe"
				["LeadSource"]=>
				string(6) "Series"
			}
		}

		// getHeadings()
		array(3) {
			string(9) "FirstName"
			string(8) "LastName"
			string(10) "LeadSource"
		}
		~~~

		#### Transform callbacks

		Usage of `setTransforms()`, `addTransform()`. The contacts in Salesforce are *John Doe* and *Jane Doe*.

		~~~php
		// callable variable
		$transformFirstName = function (&$value, $record, $records) {
			if ($value === 'Jane' && $record['LastName'] === 'Doe') {
				$value = 'Sarah';
			}
		};

		$query = $this->get('salesforce.data.query')->create()
		->addField('FirstName')
		->addField('LastName')
		->setTransforms(array(
			'LastName' => function(&$value, $record, $records) {
				if ($record['FirstName'] === 'Sarah' && $value === 'Doe') {
					$value = 'Connor';
				}
			}
		))
		->addTransform('FirstName', $transformFirstName)
		->process();

		// transforms are called following the fields order
		// here, the transform callback for 'FirstName' will be called first
		// then will be called the transform callback for 'LastName'
		// even if the addTransform() for 'FirstName' appears after the one for 'LastName'

		var_dump($query->getRecords());
		~~~

		Result

		~~~php
		array(2) {
			[0]=>
			array(2) {
				["FirstName"]=>
				string(4) "John"
				["LastName"]=>
				string(3) "Doe"
			}
			[1]=>
			array(2) {
				["FirstName"]=>
				string(4) "Sarah"
				["LastName"]=>
				string(3) "Connor"
			}
		}
		~~~

		The transform callback must have the prototype :

		~~~php
		function (&$value, $record, $records)
		~~~

		- `&$value` is the field value of the record being processed, **it must be passed by reference `&`**
		- `$record` is the record being processed (optional if not needed)
		- `$records` is the entire records result (optional if not needed)

		**The return value of the callback is ignored.** To change the `$value` you have to pass it by reference `&$value`.

		The transform callback may be a anonymous function, a callable (anonymous function inside a variable) or a **public** method in the flow class. For public method, pass it as `array($this, 'methodName')` :

		~~~php
		<?php

		use Skyflow\Flow\AbstractFlow;

		class MyFlow extends AbstractFlow
		{
			/**
			 * The transform callback for the FirstName field.
			 *
			 * It MUST be public or it won't be executed.
			 *
			 * @param mixed &$value  The field value of the record
			 *                       being processed passed by reference.
			 * @param array $record  The record being processed.
			 * @param array $records The entire records result.
			 */
			public function transformFirstName(&$value, $record, $records)
			{
				if ($value === 'Jane' && $record['LastName'] === 'Doe') {
					$value = 'Sarah';
				}
			}

			public function event($requestJson)
			{
				$this->run();
			}

			public function run()
			{
				// array with [0]=>flow instance, [1]=>method name
				$transformFirstName = array($this, 'transformFirstName');
				$transformLastName = function (&$value, $record, $records) {
					if ($record['FirstName'] === 'Sarah' && $value === 'Doe') {
						$value = 'Connor';
					}
				};

				$query = $this->get('salesforce.data.query')->create()
				->addField('FirstName', null, $transformFirstName)
				->addField('LastName', null, $transformLastName)
				->process();

				return $query->getRecords();
			}
		}
		~~~

		#### Querying through related objects

		Usage of `addField()` with related objects field :

		~~~php
		$query = $this->get('salesforce.data.query')->create()
		->addField('FirstName')
		->addField('LastName')
		->addField('Account.Name')
		->process();

		var_dump($query->getRecords());
		~~~

		Result

		~~~php
		array(2) {
			[0]=>
			array(3) {
				["FirstName"]=>
				string(4) "John"
				["LastName"]=>
				string(3) "Doe"
				["Account.Name"]=>
				string(22) "Fox Television Studios"
			}
			[1]=>
			array(3) {
				["FirstName"]=>
				string(4) "Jane"
				["LastName"]=>
				string(3) "Doe"
				["Account.Name"]=>
				string(17) "RHI Entertainment"
			}
		}
		~~~

2. SObjects service to create, update and delete an SObject record.

	~~~php
	$id = $this->get('salesforce.data.sobjects')->create('Contact', array(
		'FirstName' => 'John',
		'LastName' => 'Doe'
	));

	$this->get('salesforce.data.sobjects')->update('Contact', $id, array(
		'FirstName' => 'Jane'
	));

	$this->get('salesforce.data.sobjects')->delete('Contact', $id);
	~~~


#### Wave addon

1. Data service to send SAQL query to Wave using the query() method.

	~~~php
	$response = $this->get('wave.data')->query(
	    'q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";'
	    . 'q = filter q by Email in ["john.doe@gmail.com"];'
	    . 'q = foreach q generate FirstName as FirstName, LastName as LastName'
	);

	var_dump($response);
	~~~

	Result

	~~~php
	array(5) {
		["action"]=>
		string(5) "query"
		["responseId"]=>
		string(22) "4-AV-2ZIIVEDWfVeRhrvXV"
		["results"]=>
		array(1) {
			["records"]=>
			array(1) {
				[0]=>
				array(2) {
					["FirstName"]=>
					string(4) "John"
					["LastName"]=>
					string(3) "Doe"
				}
			}
		}
		["query"]=>
		string(166) "q = load "0FbB00000005KJEFB3/0FcB00000005X4bKLE";q = filter q by Email in ["john.doe@gmail.com"];q = foreach q generate FirstName as FirstName, LastName as LastName"
		["responseTime"]=>
		int(254)
	}
	~~~


2. External data service to upload a dataset to Wave Analytics.

	~~~php
	// The dataset uploaded is a 10x10 multiplication table.

	$dataset = $this->get('wave.externaldata')->create(
		'Multiplication',
		array(
			'edgemartAlias' => 'Multiplication',
			'format' => 'Csv',
			'operation' => 'Overwrite',
			'notificationSent' => 'Warnings',
			'notificationEmail' => 'john.doe@gmail.com',
			'description' => 'Multiplication table'
		)
	);

	// 1 row for column headers
	$dataset->appendCsvLine(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));

	// 10 rows multiplication table.
	for ($i = 1; $i <= 10; $i++) {
		$row = array();

		// 10 columns multiplication table
		for ($j = 1; $j <= 10; $j++) {
			$row[$j] = $i * $j;
		}

		$dataset->appendCsvLine($row);
	}

	$dataset->process();
	~~~

## Setup events & Skyflow API

1. Create a new Event from the Skyflow web interface
2. Map it to a flow
3. Setup a new Skyflow Token from the Home page

That's it, your API is ready to use:

POST on:

~~~
https://yourskyflowapp.herokuapp.com/api/event/EVENT_NAME
~~~

Header :

~~~
Content-Type : application/json
Skyflow-Token : GENERATED_TOKEN
~~~

Body:

~~~json
{
	"datafield" : "data..."
	//Any JSON data
}
~~~

This JSON will be catched by the implemented Flow method

~~~php
public function event($requestJson)
{
	// your code here
}
~~~
