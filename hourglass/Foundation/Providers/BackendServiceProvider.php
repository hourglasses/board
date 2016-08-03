<?php namespace Hourglass\Foundation\Providers;

use Hourglass\Http\Middleware\Authenticate;
use Hourglass\Http\Middleware\TestPermissions;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Route;
use View;

class BackendServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register backend routes
        $this->registerRoutes();
        $this->registerViewNamespace();
    }

    public function boot(Router $router)
    {
        $router->middleware('auth', Authenticate::class);
        $router->middleware('permission', TestPermissions::class);

        $router->middlewareGroup('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
        ]);
    }

    protected function registerRoutes()
    {
        Route::group(['namespace' => 'Hourglass\Http\Controllers'], function() {
            require $this->basePath('Support/SupportingFiles/routes.php');
        });
    }

    protected function registerViewNamespace()
    {
        View::addNamespace('Backend', $this->basePath('Support/SupportingFiles/resources/views'));
    }

    protected function basePath($file)
    {
        $postfix = '/' . ltrim($file, '/');
        return base_path('hourglass' . $postfix);
    }
}