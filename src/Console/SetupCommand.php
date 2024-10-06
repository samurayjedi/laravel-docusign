<?php

namespace Samurayjedi\LaravelDocusign\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SetupCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docusign:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the configuration sample into your .env file';

    /**
     * Template of configuration
     */
    protected $config = <<<PIWI
\n
DS_CLIENT_ID=Your_integration_key
DS_CLIENT_SECRET=Your_secret_key
DS_AUTHORIZATION_SERVER=account-d.docusign.com
DS_IMPERSONATED_USER_ID=Your_user_ID
DS_PRIVATE_KEY_FILE=private_dev.key
ALLOW_SILENT_AUTHENTICATION=true
DS_BRAND_ID=Your_brand_id
ADMIN_NAMES=samurayjedi,kroqgar
ADMIN_EMAILS=samurayjedi_example@gmail.com,kroqgar_example@gmail.com
PIWI;

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle() {
        /** .env, copy only the necesary config keys, if no exists, do nothing */
        $path = base_path('.env');
        if (!file_exists($path)) {
            $this->error('.env file no found!!!');
        } else {
            $env = file_get_contents($path);
            $alreadySetup = false;
            foreach ([
                'DS_CLIENT_ID',
                'DS_CLIENT_SECRET',
                'DS_AUTHORIZATION_SERVER',
                'DS_IMPERSONATED_USER_ID',
                'DS_PRIVATE_KEY_FILE',
                'ALLOW_SILENT_AUTHENTICATION',
                'DS_BRAND_ID',
                'ADMIN_NAMES',
                'ADMIN_EMAILS',
            ] as $key) {
                if (strpos($env, $key) !== false) {
                    $alreadySetup = true;
                    break;
                }
            }
            if ($alreadySetup) {
                $this->error('Docusign configuration already present into .env file.');
            } else {
                file_put_contents($path, $this->config, FILE_APPEND);
                $this->info('Docusign configuration template added to the end of .env file.');
            }
            /** routes file, if not exists, copy the init template */
            $routesPath = base_path('routes/docusign.php');
            if (!file_exists($routesPath)) {
                copy(__DIR__.'/../../stubs/docusign.php', $routesPath);
            }
            /** middleware file, if no exits, copy the init template */
            $folder = 'app/Http/Middleware';
            $middlewarePath = base_path("$folder/IsAllowedByDocuSign.php");
            if (!file_exists($middlewarePath)) {
                if (!file_exists($folder)) {
                    mkdir("folder/" . $dirname, 0755);
                }
                copy(__DIR__.'/../../stubs/IsAllowedByDocuSign.php', $middlewarePath);

                $this->installMiddleware(
                    ['\App\Http\Middleware\IsAllowedByDocusign::class'],
                    'appendToGroup',
                    'auth.docusign',
                );
            }
        }
    }

    protected function installMiddleware($names, $group = 'web', $modifier = 'append')
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $names = collect(Arr::wrap($names))
            ->filter(fn ($name) => ! Str::contains($bootstrapApp, $name))
            ->whenNotEmpty(function ($names) use ($bootstrapApp, $group, $modifier) {
                $names = $names->map(fn ($name) => "$name")->implode(','.PHP_EOL.'            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                        .PHP_EOL."        \$middleware->$group('$modifier', ["
                        .PHP_EOL."            $names,"
                        .PHP_EOL.'        ]);'
                        .PHP_EOL,
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
    }
}
