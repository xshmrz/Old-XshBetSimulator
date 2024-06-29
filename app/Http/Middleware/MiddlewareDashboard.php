<?php
    namespace App\Http\Middleware;
    use Closure;
    use Illuminate\Http\Request;
    class MiddlewareDashboard {
        public function handle(Request $request, Closure $next) {
            # -> if (Authorize()->session()):
            # -> else:
            # ->     return redirect()->route("dashboard.login");
            # -> endif;
            return $next($request);
        }
    }
