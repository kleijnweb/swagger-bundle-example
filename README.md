# KleijnWeb\SwaggerBundle Example 
[![Build Status](https://travis-ci.org/kleijnweb/swagger-bundle-example.svg?branch=master)](https://travis-ci.org/kleijnweb/swagger-bundle-example)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/?branch=master)

Example for [SwaggerBundle](https://github.com/kleijnweb/swagger-bundle) 4.0.

## Run The Example

Download and install docker-compose. After `docker-compose up`, setup the database:

```bash
docker-compose run app bash -c 'app/console doctrine:database:create --no-interaction && app/console doctrine:migrations:migrate --no-interaction && app/console doctrine:fixtures:load --no-interaction'
```
## License

MIT
