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

```
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

```
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

