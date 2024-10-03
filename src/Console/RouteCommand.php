<?php

namespace Samurayjedi\LaravelDocusign\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RouteCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docusign:route {url : URL of the route.}
                            {name : Name of the route}
                            {controller : Controller class}
                            {method : Controller handler method}
                            {--create-controller : Create a controller class file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Integrate docusign into your laravel application';

    /**
     * Template of the route
     * @var string
     */
    protected $routeTemplate = <<<PIWI
    Route::get('%s', [
        App\Http\Controllers\%s::class,
        '%s',
    ])->name('%s');\n
PIWI;

    /**
     * Template of the controller
     * @var string
     */

     protected $controllerTemplate = <<<'PIWI'
<?php

namespace App\Http\Controllers;

class %s extends Controller {
    public function %s() {
        return redirect('/');
    }
}
PIWI;

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle() {
        // (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Http/Controllers', app_path('Http/Controllers'));
        // (new Filesystem)->ensureDirectoryExists(app_path('Http/Middleware'));
        // (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Http/Middleware', app_path('Http/Middleware'));
        $routesPath = base_path('routes/docusign.php');
        if (!file_exists($routesPath)) {
            copy(__DIR__.'/../../stubs/docusign.php', $routesPath);
        }
        $routes = file_get_contents($routesPath);
        $pos = strrpos($routes, '});');
        $code = sprintf(
            $this->routeTemplate,
            $this->argument('url'),
            $this->argument('controller'),
            $this->argument('method'),
            $this->argument('name'),
        );
        file_put_contents($routesPath, substr_replace($routes, $code, $pos, 0));
        if ($this->option('create-controller')) {
            $controllerPath = base_path('app/Http/Controllers/'.$this->argument('controller').'.php');
            if (!file_exists($controllerPath)) {
                file_put_contents(
                    $controllerPath, 
                    sprintf(
                        $this->controllerTemplate,
                        $this->argument('controller'),
                        $this->argument('method'),
                    ),
                );
            } else {
                $this->error('File "'.$this->argument('controller').'.php" already exists!!!');
            }
        }
    }
}
