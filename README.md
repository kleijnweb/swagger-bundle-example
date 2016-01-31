# Symfony Bundle Example [![Build Status](https://travis-ci.org/kleijnweb/swagger-bundle-example.svg?branch=master)](https://travis-ci.org/kleijnweb/swagger-bundle-example) 

"Kitchen Sink" example for SwaggerBundle with support for E-Tags and JWT.

## Run The Example

Download and install docker-compose. After `docker compose up`, setup the database:

Go to `http://localhost:8000/#/?import=/swagger/service-desk/v1.yml` to get a Swagger Editor where yoou can try out the API.

You'll need the following token:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImRlZmF1bHQifQ.eyJpc3MiOiJ0ZXN0aW5nX2lzc3VlciIsInBybiI6ImFwaSJ9.o4tBedoktxALvXKRR3_M3Hq2XUMAwHiUTr2sK85yehQ
```

To play around with JWT tokens, use jwt.io.


## License

MIT
