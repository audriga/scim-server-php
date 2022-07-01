# scim-server-php

**scim-server-php** is a PHP library making it easy to implement [SCIM v2.0](https://datatracker.ietf.org/wg/scim/documents/) server endpoints for various systems.

It is built on the following IETF approved RFCs: [RFC7642](https://datatracker.ietf.org/doc/html/rfc7642), [RFC7643](https://datatracker.ietf.org/doc/html/rfc7643) and [RFC7644](https://datatracker.ietf.org/doc/html/rfc7644)

This is a **work in progress** project. It already works pretty well but some features will be added in the future and some bugs may still be arround 😉

The **scim-server-php** project currently includes the following:

* A SCIM 2.0 server core library
* A [Postfix Admin](https://github.com/postfixadmin/postfixadmin) SCIM API

**scim-server-php** also comes with an integrated Mock SCIM server based on a SQLite database.

## SCIM 2.0 server core library

This library provides:

* Standard SCIM resources implementations (*Core User*, *Enterprise User* and *Groups*)
* Custom SCIM resource *Provisioning User* implementation
* Standard CRUD operation on above SCIM resources
* A HTTP server handling requests and responses on defined endpoints, based on the [Slim](https://www.slimframework.com/) framework
* A very simple JWT implementation
    * When enabled, a JWT token is generated on the `/jwt` endpoint. You **must** therefore protect this endpoint.
    * When enabled, this JWT token needs to be provided in all requests using the Bearer schema (`Authorization: Bearer <token>`)
* An easily reusable code architecture for implementing SCIM servers

## Postfix Admin SCIM API

The [Postfix Admin](https://github.com/postfixadmin/postfixadmin) API enables SCIM server capabilities for [Postfix Admin](https://github.com/postfixadmin/postfixadmin). It uses the core library above.

It supports standard GET, POST, PUT and DELETE operations on SCIM *Provisioning User* resources, which are translated in the corresponding operations on the [Postfix Admin](https://github.com/postfixadmin/postfixadmin) mailboxes.

Example (null values removed for readability):

```
$ curl https://my.postfix.admin.url/Users/aaaa@bli.fr -H 'Authorization: Bearer <token>'
{
   "schemas":[
      "urn:ietf:params:scim:schemas:core:2.0:User",
      "urn:audriga:params:scim:schemas:extension:provisioning:2.0:User"
   ],
   "id":"aaaa@bli.fr",
   "meta":{
      "resourceType":"User",
      "created":"2022-05-27 12:45:08",
      "location":"https://my.postfix.admin.url/Users/aaaa@bli.fr",
      "updated":"2022-06-15 13:07:30"
   },
   "userName":"aaaa@bli.fr",
   "name":{
      "formatted":"Aaaa"
   },
   "displayName":"Aaaa",
   "active":"1",
   "urn:audriga:params:scim:schemas:extension:provisioning:2.0:User":{
      "sizeQuota":51200000
   }
}
```

## Prerequisites
* **scim-server-php** requires PHP 7.4
* Dependencies are managed with [composer](https://getcomposer.org/)

## Installation
### Local installation
* Run `make install` to automatically install dependencies

### Configuration
* To switch from the Mock SCIM server to the Postfix Admin SCIM API, you simply need to adapt the `public/index.php` file (include **one** of the following):

```
// Set up system-specific dependencies
$dependencies = require dirname(__DIR__) . '/src/Dependencies/mock-dependencies.php'; // include that line if you want to use the integrated mock SCIM server
$dependencies = require dirname(__DIR__) . '/src/Dependencies/pfa-dependencies.php'; // include that line if you want to use the Postfix Admin SCIM API
```

## Acknowledgements

This software is part of the [Open Provisioning Framework](https://www.audriga.com/en/User_provisioning/Open_Provisioning_Framework) project that has received funding from the European Union's Horizon 2020 research and innovation program under grant agreement No. 871498.