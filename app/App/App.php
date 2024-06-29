<?php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route;
    use Jenssegers\Agent\Facades\Agent;
    # ->
    const PWASITE      = false;
    const PWAPANEL     = false;
    const PWADASHBOARD = false;
    const MODULE       = "module";
    const REDIRECT     = "redirect";
    const VALIDATION   = "validation";
    const ID           = "id";
    const DASHBOARD    = "Dashboard";
    const PANEL        = "Panel";
    const SITE         = "Site";
    # ->
    function getView() {
        $controller   = getAction()['controller'];
        $method       = getAction()['method'];
        $prefix       = (agentIsMobile() && agentIsTablet() && (PWASITE || PWAPANEL || PWADASHBOARD)) ? "Mobile" : "Desktop";
        $replacements = ["Site" => "Site\\$prefix", "Panel" => "Panel\\$prefix", "Dashboard" => "Dashboard\\$prefix"];
        $controller   = Str::replace(array_keys($replacements), array_values($replacements), $controller);
        $view         = Str::replace("\\", ".", $controller."\\".$method);
        return view($view);
    }
    function getAction() {
        $action = Str::replace("App\\Http\\Controllers\\", "", Route::getCurrentRoute()->getActionName());
        $action = Str::replace(["Core\\", "Base\\"], "", $action);
        [$controller, $method] = explode("@", $action);
        return ["controller" => $controller, "method" => $method];
    }
    function getMethod() {
        return getAction()['method'];
    }
    function getMethodName() {
        return Str::title(getMethod());
    }
    function getController() {
        return getAction()['controller'];
    }
    function getControllerName() {
        return Str::replace("\\", " / ", getController());
    }
    function getModule() {
        if (isDashboard()):
            return DASHBOARD;
        endif;
        if (isPanel()):
            return PANEL;
        endif;
        if (isSite()):
            return SITE;
        endif;
    }
    # ->
    function isDashboard() {
        return Str::contains(getController(), DASHBOARD);
    }
    function isPanel() {
        return Str::contains(getController(), PANEL);
    }
    function isSite() {
        return Str::contains(getController(), SITE);
    }
    # ->
    function agentIsMobile() {
        return Agent::isMobile();
    }
    function agentIsTablet() {
        return Agent::isTablet();
    }
    function agentIsDesktop() {
        return Agent::isDesktop();
    }
    # ->
    function randomImage($width, $height, $unsplash = EnumStatus::Passive) {
        if ($unsplash == EnumStatus::Active) {
            return "https://unsplash.it/".$width."/".$height."?".rand(1000, 2000);
        }
        else {
            if (!file_exists('assets/placeholder/'.$width.'x'.$height.".png")) {
                $image = file_get_contents('https://dummyimage.com/'.$width.'x'.$height.'/CECECE/595959.png');
                file_put_contents('assets/placeholder/'.$width.'x'.$height.'.png', $image);
                return 'assets/placeholder/'.$width.'x'.$height.".png";
            }
            else {
                return 'assets/placeholder/'.$width.'x'.$height.".png";
            }
        }
    }
    # ->
    function Form() {
        return new \Galahad\Aire\Support\Facades\Aire();
    }
    function Authorize() {
        return new \App\Models\Authorize();
    }
    # ->
    function requestHas($key) {
        if (request()->has($key)) :
            return true;
        else:
            return false;
        endif;
    }
    function requestHasAndEqual($key, $value) {
        if (request()->has($key)) :
            if (request($key) == $value):
                return true;
            else:
                return false;
            endif;
        else:
            return false;
        endif;
    }



