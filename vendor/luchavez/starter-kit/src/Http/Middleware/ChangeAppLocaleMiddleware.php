<?php

namespace Luchavez\StarterKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Class ChangeAppLocaleMiddleware
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class ChangeAppLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $param = starterKit()->getChangeLocaleKey();

        if (starterKit()->isChangeLocaleEnabled() && $request->has($param)) {
            // Get lang from Request
            $lang = $request->get($param, App::getLocale());

            // Set both App and Request locale
            App::setLocale($lang);
            $request->setLocale($lang);

            // Remove lang from Request query and request
            $request->query->remove($param);
            $request->request->remove($param);
        }

        return $next($request);
    }
}
