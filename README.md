## About

Its a package that i have create to make me easier integrate docusign through my projects.

## How to install

simple as run:

```bash
composer require samurayjedi/laravel-docusign
```

## Configure

First ensure the .env file exits in your project, then run:

```bash
php artisan docusign:setup
```

This create the middleware '~/app/Http/Middleware/IsAllowedByDocuSign.php', it redirect to docusign passing as parameter our app integration data and as return url the actual/requested route, if docusing 'consent', redirect to the return url, if is the first time running the app, docusign ask you if you want grant permissions to the 'x' app works with you account, once its done, the future request will be transparent.

In addition, the command add to your '~/bootstrap/app.php' the following lines for register the previous middleware:

```php
// ...
->withMiddleware(function (Middleware $middleware) {
    $middleware->appendToGroup('auth.docusign', [
        \App\Http\Middleware\IsAllowedByDocusign::class,
    ]);

    //
})
// ...
```

Important note: the command finds '->withMiddleware(function (Middleware $middleware) {' coincidence into the file and append, into the brackets, the middleware, in my uses, it works fine every time, but i recommend you backup your '~/bootstrap/app.php' before running the command, if you have made changes to the file.

Futhermore, the artisan command create '~/routes/docusign.php', it contains:

```php
Route::middleware(['web', 'auth', 'auth.docusign'])->group(function () {
    
});
```

Inside i put my routes that need docusign consent (both GET and POST).

Finally and the most important (and boresome) part, the command too puth into your .env file the following lines:

```
DS_CLIENT_ID=Your_integration_key
DS_CLIENT_SECRET=Your_secret_key
DS_AUTHORIZATION_SERVER=account-d.docusign.com
DS_IMPERSONATED_USER_ID=Your_user_ID
DS_PRIVATE_KEY_FILE=private_dev.key
ALLOW_SILENT_AUTHENTICATION=true
DS_BRAND_ID=Your_brand_id
ADMIN_NAMES=samurayjedi,kroqgar
ADMIN_EMAILS=samurayjedi_example@gmail.com,kroqgar_example@gmail.com
```

to get each one, login to you docusign developer account and enter in 'App & Keys':

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/how_to_get_in.png" alt="App & Keys">

</div>

create a app:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/create_app.png" alt="App & Keys">

</div>

DS_CLIENT_SECRET is found:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/secret.png" alt="App & Keys">

</div>

Create RSA keys pair:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/rsa1.png" alt="App & Keys">

</div>

And copy public and private key and put it into a file, put the private key file into '~/storage/docusign/', and change this line with the name of your file:

```
// ...
DS_PRIVATE_KEY_FILE=private_dev.key
// ...
```

For my app to work in local, I had to put all this uris, i don't remember well, but in production i believe i had to change 'localhost' with the domain:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/uris.png" alt="App & Keys">

</div>

Finally, save the changes.

Still missing the DS_CLIENT_ID, it is found:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/ds_client_id.png" alt="App & Keys">

</div>

DS_IMPERSONATED_USER_ID is found:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/ds_impersonated_user_id.png" alt="App & Keys">

</div>

DS_BRAND_ID, required you to create a brand, got to Brands -> Add Brand and fill the form:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/get_brand.png" alt="App & Keys">

</div>

when all its done, copy the brand id:

<div align="center">

<img src="https://github.com/samurayjedi/laravel-docusign/blob/main/how_to/ds_brand_id.png" alt="App & Keys">

</div>

Its done, docusign its integrated to your app finally!!! (in theory :p)

