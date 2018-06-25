# php-poc-geoapi

[![Build Status](https://travis-ci.org/brunoric/php-poc-geoapi.svg?branch=master)](https://travis-ci.org/brunoric/php-poc-geoapi)

Very tiny Proof of Concept for calculating distances between two points in the globe. This application provides a small
REST API to play with some customer data and their location.

## Installing and playing

To install and play this PoC you will just need to have a PHP 7.1+ environment with composer installed and then run the
following command in your CLI:

````
composer create-project brunoric/php-poc-geoapi
````

To start playing with it, run the command below from inside the project path:

````
bin/console server:start
````

This will run the PHP built-in server making the application available at http://127.0.0.1:8000.

## Customer list

To list the customers from the default customer repository access the endpoint \*:

````
http://127.0.0.1:8000/customer/list
````

You can then play with the customer properties (id, name, latitude, longitude) and with 'asc' or 'desc' as ordering
direction.

````
# will output the customers order by ID ascendent.
http://127.0.0.1:8000/customer/list/id/asc

# will output the customers order by ID descentend.
http://127.0.0.1:8000/customer/list/id/desc

# will output the customers order by NAME ascendent.
http://127.0.0.1:8000/customer/list/name/asc

# will output the customers order by NAME descentend.
http://127.0.0.1:8000/customer/list/id/desc

# will output the customers order by LATITUDE ascendent.
http://127.0.0.1:8000/customer/list/id/asc

# will output the customers order by LATITUDE descentend.
http://127.0.0.1:8000/customer/list/id/desc

# will output the customers order by LONGITUDE ascendent.
http://127.0.0.1:8000/customer/list/id/asc

# will output the customers order by LONGITUDE descentend.
http://127.0.0.1:8000/customer/list/id/desc
````

*To order the customers by distance from a specific point just call \*\*:*

````
# will output the customers order by DISTANCE ascendent.
http://127.0.0.1:8000/customer/list/distance/asc

# will output the customers order by DISTANCE descentend.
http://127.0.0.1:8000/customer/list/distance/desc
````

_\* Current customers are being loaded from a closed list in a S3 bucket._

_\* The default latitude is 53.339428 and the default longitude is -6.257664._


