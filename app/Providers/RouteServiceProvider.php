<?php
    namespace App\Providers;
    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Support\Facades\Route;
    class RouteServiceProvider extends ServiceProvider {
        public function boot() : void {
            $this->loadRoutesFrom(base_path("routes")."/core.php");
            $this->loadRoutesFrom(base_path("routes")."/api.php");
            $this->loadRoutesFrom(base_path("routes")."/channels.php");
            $this->loadRoutesFrom(base_path("routes")."/console.php");
            $this->loadRoutesFrom(base_path("routes")."/web.php");
        }
    }
