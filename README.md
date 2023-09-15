# OAuth flows demo

Run docker environment
```bash
cd [my-app-name]
docker-compose up -d
```

Inside `demo-php-fpm` container run `cd slim-app; composer install;`

## Client credentials flow
There's simple CLI php application in `slim-app/cli/example.php` which has single `example` command available.
Run it from inside the `demo-php-fpm` container
```bash
php cli/example.php example
```
The cli application will try to call some endpoint `/client-credentials/resource-a` required authorization of the regular web application in this project.
In this case [Client credentials flow](https://auth0.com/docs/get-started/authentication-and-authorization-flow/client-credentials-flow) implemented.

## Authorization Code flow

To initiate this flow try to access open `http://phpfpm.loc:8000/web-app-auth/page-one` in your browser.
Next you have to be redirected to login page. After successful _sign up_ and _sign in_ actions the page mentioned above should be accessible.

**NOTE:** For in both cases [Auth0-PHP SDK](https://auth0.com/docs/libraries/auth0-php) library is used to call auth endpoint and validate the responses.<br>

**NOTE** By default for _Authorization code flow_ by default Proof Key Code Exchange is enabled.
[Learn more](https://auth0.com/docs/get-started/authentication-and-authorization-flow/authorization-code-flow-with-proof-key-for-code-exchange-pkce).