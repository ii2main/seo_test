<?php

namespace App\Providers;

use App\Contracts\RankProviderInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Services\DataForSeoService;
use GuzzleHttp\Client;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return new Client([
                'timeout' => 60,
                'connect_timeout' => 10,
            ]);
        });

        $this->app->bind(RankProviderInterface::class, function ($app) {
            $provider = config('services.rank_provider', 'dataforseo');

            return match ($provider) {
                'dataforseo' => new DataForSeoService($app->make(Client::class)),
                default => throw new InvalidArgumentException('Unknown rank provider: ' . $provider),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();
        //
    }
}
