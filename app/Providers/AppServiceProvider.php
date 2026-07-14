<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\ProductAuditLogRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Repositories\AuthRepository;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductAuditLogRepository;
use App\Repositories\ProductRepository;
use Dedoc\Scramble\Scramble;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Use the project's custom exception Handler (Laravel 13 does not auto-bind it).
        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductAuditLogRepositoryInterface::class, ProductAuditLogRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    public function boot(): void
    {
        $acceptLang = request()->server('HTTP_ACCEPT_LANGUAGE') ?? 'en';
        $locale = substr($acceptLang, 0, 2);

        if (in_array($locale, ['en', 'fr', 'es'], true)) {
            AppFacade::setLocale($locale);
        } else {
            AppFacade::setLocale('en');
        }

        Product::observe(ProductObserver::class);

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            });
    }
}
