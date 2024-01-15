# This project is no longer maintained

# KleijnWeb\SwaggerBundle Example 
[![Build Status](https://travis-ci.org/kleijnweb/swagger-bundle-example.svg?branch=master)](https://travis-ci.org/kleijnweb/swagger-bundle-example)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/?branch=master)

## What's this?

Example for [SwaggerBundle](https://github.com/kleijnweb/swagger-bundle) 4.0 with [support for JWT authentication](https://github.com/kleijnweb/jwt-bundle). 

The example app is an extremely basic ticketing system. You can create, search and modify tickets.

## Setup

Running the examples requires docker-engine and docker-compose.

After `docker-compose up`, initialize the database:

```bash
docker-compose run app bash -c 'app/console doctrine:database:create --no-interaction && app/console doctrine:migrations:migrate --no-interaction && app/console doctrine:fixtures:load --no-interaction'
```

## Usage

Navigate to http://localhost:8000/ and you can play around with the API.

Fetching a ticket by ID or ticket number is unsecured. Searching for tickets and creating a ticket requires ROLE_USER, modifying and deleting tickets requires ROLE_ADMIN when using a different user (`sub` claim). The fixture tickets are created with `sub` "john". Fetching the ticket count by status always requires ROLE_ADMIN. 

You'll need the following tokens:

With **ROLE_USER** (`sub` "john"):

> eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJqb2huIiwiaXNzIjoiZGV2c2VydmVyIiwiYXVkIjpbInVzZXIiXX0.SeDqiYtJ0S2smaU-kRJrSh_OucpImmbG3Vux35Qk948

Or (`sub` "jane"):

> eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJqYW5lIiwiaXNzIjoiZGV2c2VydmVyIiwiYXVkIjpbInVzZXIiXX0.4_D5H1FSbHonbqhR33kYK6MPa3BWtUNSdQCDV-btYNo


With **ROLE_ADMIN**  (`sub` "dick"):

> eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJkaWNrIiwiaXNzIjoiZGV2c2VydmVyIiwiYXVkIjpbImFkbWluIl19.NvzX-7y-3oW0_gaRX_Pxe_qpPfLHvM54vx_D527mj8U

## License

MIT
