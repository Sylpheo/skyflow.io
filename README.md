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

        heroku login
		heroku pg:psql -a your-app-name < install.sql

3. Open the application in a browser (or go to **https://your-app-name.herokuapp.com/** where *your-app-name* is your heroku application name).

        heroku open -a your-app-name

4. Login using the default skyflow admin user

        username: skyflow
        password: skyflow

## Setup the Skyflow addons

In order to communicate with the different third-party platforms (such as Salesforce and Wave) you will need to setup the corresponding addons and provide your connected applications client id and client secret. You need to do this once.

## Create your first flow

1. Create your flow class in the *src/Skyflow/Flow* directory.
2. Setup the addons in the home page. You will need to provide your connected application client id and client secret in order to use the addon.
3. Declare your flow using the Skyflow web interface with the namespace: skyflow\Flow\MyFlow

## Use flow services

	<?php

	namespace Skyflow\Flow;

	use Skyflow\Flow\AbstractFlow;

	/**
	 * My first flow class.
	 */
	class MyFirstFlow extends AbstractFlow
	{
		/**
	     * The code that will be executed when a HTTP POST request is sent to
	     * https://your-app-name.heroku.com/api/event/MyFirstFlow
	     * Assuming you created an event named MyFirstFlow and a mapping
	     * between the event MyFirstFlow and this flow.
	     *
	     * @param $jsonData The JSON data.
	     */
		public function event($jsonData)
	    {
	        return $this->run();
	    }

	    /**
	     * The code that will be run via Heroku Scheduler.
	     */
	    public function run()
	    {
			// Send a SOQL query to Salesforce.
	        // Be sure the Salesforce addon is setup with client id and client secret
			// or this will not work.
	        // At this point your query must be well formed or it will fail.

	        $resultSalesforce = $this->getSalesforce()->getData()->query('SELECT Name FROM Account LIMIT 1');

	        // Send a SAQL query to Wave.
	        // Be sure the Wave addon is setup with client id and client secret or
	        // this will not work.
			// At this point your query must be well formed or it will fail.

	        $resultWave = $this->getWave()->getData()->query(
				'q = load "0FbB000010E3KLS/0FcB0000000FBQLMUZ7";'
	            . 'q = filter q by Email in ["my-email@gmail.com"];'
	            . 'q = foreach q generate FirstName as FirstName, LastName as LastName'
	        );
	    }
	}

### Available services by addon :

#### Salesforce addon

1. Data service to send SOQL query to Salesforce using the query() method.

#### Wave addon

1. Data service to send SAQL query to Wave using the query() method.

## Setup events & Skyflow API

1. Create a new Event from Skyflow console interface
2. Map it to a flow from the console
3. Setup a new Skyflow Token from the console Home page

That's it, your API is ready to use:

POST on:

	https://yourskyflowapp.herokuapp.com/api/event/EVENT_NAME

Header :

	Content-Type : application/json
	Skyflow-Token : GENERATED_TOKEN

Body:

	{
		"datafield" : "data..."
		//Any JSON data
	}

This JSON will be catched by the implemented Flow method "event($jsonData)"
