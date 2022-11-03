# scim-server-php

This is the Open Provisioning Framework project by audriga which makes use of the [SCIM](http://www.simplecloud.info/) protocol.

---

# Table of Contents
1. [Info](#info)
1. [Related projects](#related-projects)
1. [Capabilities](#capabilities)
1. [Prerequisites](#prerequisites)
1. [Usage](#usage)
    1. [Get it as a composer dependency](#get-it-as-a-composer-dependency)
    1. [Try out the embedded mock server](#try-out-the-embedded-mock-server)
        1. [Enable JWT authentication](#enable-jwt-authentication)
    1. [Use scim-server-php for your own project](#use-scim-server-php-for-your-own-project)
        1. [SCIM resources](#scim-resources)
        1. [SCIM server](#scim-server)
    1. [Authentication/Authorization](#authenticationauthorization)
        1. [Define your authentication/authorization logic](#define-your-authenticationauthorization-logic)
        1. [Define your authentication/authorization middleware](#define-your-authenticationauthorization-middleware)
        1. [Add your authentication/authorization middleware to the SCIM server](#add-your-authenticationauthorization-middleware-to-the-scim-server)
    1. [Full example](#full-example)
1. [Acknowledgements](#acknowledgements)

---

## Info

**scim-server-php** is a PHP library which makes it easy to implement [SCIM v2.0](https://datatracker.ietf.org/wg/scim/documents/) server endpoints for various systems.

It is built on the following IETF approved RFCs: [RFC7642](https://datatracker.ietf.org/doc/html/rfc7642), [RFC7643](https://datatracker.ietf.org/doc/html/rfc7643) and [RFC7644](https://datatracker.ietf.org/doc/html/rfc7644)

This is a **work in progress** project. It already works pretty well but some features will be added in the future and some bugs may still be arround 😉

The **scim-server-php** project currently includes the following:

* A SCIM 2.0 server core library
* An integrated Mock SCIM server based on a SQLite database.

## Related projects

* A [Postfix Admin](https://github.com/postfixadmin/postfixadmin) SCIM API based on **scim-server-php** is available at https://github.com/audriga/postfixadmin-scim-api
* The [Nextcloud SCIM](https://lab.libreho.st/libre.sh/scim/scimserviceprovider) application provides a SCIM API to [NextCloud](https://nextcloud.com/) and uses **scim-server-php** for its SCIM resource models

## Capabilities

This library provides:

* Standard SCIM resources implementations (*Core User*, *Enterprise User* and *Groups*)
* Custom SCIM resource *Provisioning User* implementation
* Custom SCIM resource *Domain* implementation
* Standard CRUD operations on above SCIM resources
* A HTTP server handling requests and responses on defined endpoints, based on the [Slim](https://www.slimframework.com/) framework
* A simple JWT implementation
    * When enabled, this JWT token needs to be provided in all requests using the Bearer schema (`Authorization: Bearer <token>`)
    * You can generate a token with the script located at `bin/generate_jwt.php`
    * The secret you use *must* be also defined in your `config/config.php` file
* An easily reusable code architecture for implementing SCIM servers

Note that you can of course use the standard and custom SCIM resources implementations with your own HTTP server if you don't want to use the one provided by **scim-server-php**.

## Prerequisites
* **scim-server-php** requires PHP 7.4
* Dependencies are managed with [composer](https://getcomposer.org/)

## Usage

### Get it as a [composer](https://getcomposer.org/) dependency

* You can add the following to your `composer.json` file to get it with [composer](https://getcomposer.org/)

```   
    "repositories": {
        "scim": {
            "type": "vcs",
            "url": "git@bitbucket.org:audriga/scim-server-php.git"
        }
    },
    "require": {
        "audriga/scim-server-php": "dev-master"
    },
```

* We plan to publish to [packagist](https://packagist.org/) in the future

### Try out the embedded mock server

* To help you use and understand this library, a mock server is provided
* Clone this repository
* Run `make install` to automatically install dependencies and setup a mock database
* Run `make start-server` to start a local mock SCIM server accessible on `localhost:8888`
* Send your first SCIM requests! For example, try out `curl http://localhost:8888/Users`
* It supports all basic CRUD operations on SCIM Core Users and Groups

#### Enable JWT authentication

* A very simple JWT authentication is provided
* Enable it for the embedded mock server by uncommenting the 2 following lines in `public/index.php` and restart it

```
$scimServerPhpAuthMiddleware = 'AuthMiddleware';
$scimServer->setMiddleware(array($scimServerPhpAuthMiddleware));
```

* You will now need to send a valid JWT token with all your requests to the mock server
    * A JWT token will be considered as valid by the mock server if its secret is identical to the secret set in the `jwt` section of `config/config[.default].php`
* To generate a token, use the script located at `bin/generate_jwt.php`
    * Note that this script generates a JWT token including a `user` claim set by the `--user` parameter. You can use any value here in the mock server case.

### Use scim-server-php for your own project

#### SCIM resources

* You can directly reuse the SCIM resources implementation from the `src/Models/SCIM/` folder in any PHP project
* Here are the provided resources implementations
    * `src/Models/SCIM/Standard/Users/CoreUser.php` implements the Core User resource from the SCIM standard 
    * `src/Models/SCIM/Standard/Users/EnterpriseUser.php` implements the Enterprise User extension from the SCIM standard 
    * `src/Models/SCIM/Standard/Groups/CoreGroup.php` implements the Core Group resource from the SCIM standard 
    * `src/Models/SCIM/Custom/Domains/Domain.php` implements the custom Domain resource
    * `src/Models/SCIM/Custom/Users/ProvisioningUser.php` implements the custom Provisioning User extension of the Core User

#### SCIM server

* You can use **scim-server-php** to easily create a full-fledged SCIM server for your own data source
* **scim-server-php** uses the [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html) and the [Adapter Pattern](https://en.wikipedia.org/wiki/Adapter_pattern) in order to be as flexible and portable to different systems for provisioning as possible
* You can use the embedded mock server implementation as an example ;)
* Concretelly, you will need to implement the following for each resource type of your data source
    * `Model` classes representing your resources
        * See e.g. `src/Models/Mock/MockUsers`
    * `DataAccess` classes defining how to access your data source
        * See e.g. `src/DataAccess/Users/MockUserDataAccess.php`
    * `Adapter` classes, extending `AbstractAdapter` and defining how to convert your resources to/from SCIM resources
        * See e.g. `src/Adapters/Users/MockUserAdapter.php`
    * `Repository` classes, extending `Opf\Repositories\Repository` and defining the operations available on your resources
        * See e.g. `src/Repositories/Users/MockUsersRepository.php`
    * If you want to define new SCIM resources, you will also need to implement new `Controllers` (see `src/Controllers`) and SCIM `Model`s (see `src/Models/SCIM`)

* **scim-server-php** uses [Dependency Injection Container](https://php-di.org/) internally
    * Create a `dependencies` file reusing the pattern of `src/Dependencies/mock-dependencies.php`
        * The "Auth middleware" and "Authenticators" sections are explained in the [Authentication/Authorization](#authenticationauthorization) section bellow
        * Your `Repository` classes will get the corresponding `DataAccess` and `Adapter` classes through the **scim-server-php** container

* Instantiate a `ScimServer` and feed it with your `dependencies` file as shown in `public/index.php`
    * The "Authentication Middleware" section is explained in the [Authentication/Authorization](#authenticationauthorization) section bellow

### Authentication/Authorization

#### Define your authentication/authorization logic

* Authentication is mostly delegated to the system using **scim-server-php**
    * A basic JWT based authentication implementation is provided as an example in `src/Util/Authentication/SimpleBearerAuthenticator`
    * Define your own `Authenticator` class(es) by implementing the `AuthenticatorInterface` available in `Util/Authentication`
    * A script generating a JWT token containing a single `user` claim is provided in `bin/generate_jwt.php`
* Authorization is delegated to the system using **scim-server-php**

#### Define your authentication/authorization middleware

* The **scim-server-php** HTTP server is based on the [Slim](https://www.slimframework.com/) framework and reuses its [Middleware](https://www.slimframework.com/docs/v4/concepts/middleware.html) concept
* Authentication and authorization should therefore be implemented as "Middleware(s)"
    * This means implementing the `MiddlewareInterface`
* The authentication middleware should then delegate the actual authentication process to your `Authenticator`
* The authorization implementation is up to you
    * You can either integrate it in the `Authenticator` (and so, in the authentication middleware)
    * Or you can implement an independent authentication middleware
* You can use `src/Middleware/SimpleAuthMiddleware` as an example

#### Add your authentication/authorization middleware to the SCIM server

* Add your middleware to your dependencies file
* You can use `src/Dependencies/mock-dependencies.php` as an example
* Note that the mock `SimpleAuthMiddleware` also uses the **scim-server-php** container to gets the authenticator to use
    * Hence `src/Dependencies/mock-dependencies.php` defines a `'BearerAuthenticator'` which is then used in `SimpleAuthMiddleware`

### Full example

* We advise to use https://github.com/audriga/postfixadmin-scim-api as a full **scim-server-php** implementation example

## Acknowledgements

This software is part of the [Open Provisioning Framework](https://www.audriga.com/en/User_provisioning/Open_Provisioning_Framework) project that has received funding from the European Union's Horizon 2020 research and innovation program under grant agreement No. 871498.