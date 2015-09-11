# Skyflow.io

Cloud ETL for Heroku [http://sylpheo.github.io/skyflow.io/](http://sylpheo.github.io/skyflow.io/)

## Deploying to Heroku using the Heroku Button

You can deploy your own version of Skyflow in seconds using the Heroku button below:

<a href="https://heroku.com/deploy?template=https://github.com/Sylpheo/skyflow.io">
  <img src="https://www.herokucdn.com/deploy/button.png" alt="Deploy">
</a>

Please note your heroku application name, it will be referred as *your-app-name* in the additional steps below :

1. Install the heroku toolbelt from [https://toolbelt.heroku.com/](https://toolbelt.heroku.com/).
2. Download *install.sql* from <a href="https://raw.githubusercontent.com/Sylpheo/skyflow.io/master/install.sql" download>https://raw.githubusercontent.com/Sylpheo/skyflow.io/master/install.sql</a> and provision the database :

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

1. Data service to send SOQL query to Salesforce using the query() method.

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
