<?php
    namespace App\Console\Commands;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;
    use Touhidurabir\StubGenerator\Facades\StubGenerator;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
    use Symfony\Component\Finder;
    class AppDoRoute extends Command {
        protected $signature   = 'app:app-do-route';
        protected $description = 'Command Description';
        public function handle() {
            $CONSTANTROUTE = "<?php".PHP_EOL;
            $CONSTANTROUTE .= "use Illuminate\Support\Facades\Route;".PHP_EOL;
            $CONSTANTROUTE .= "use Mcamara\LaravelLocalization\Facades\LaravelLocalization;".PHP_EOL;
            $CONSTANTROUTE .= "Route::group(['prefix' => LaravelLocalization::setLocale()], function () {".PHP_EOL;
            $TABLES        = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            # -> SYSTEM
            foreach (AppDoItEnum::$MODULES as $MODULE):
                $STUB["NAMEMODULESTUDLY"] = Str::studly($MODULE);
                $STUB["NAMEMODULE"]       = $MODULE;
                $CONSTANTROUTE            .= "Route::group(['as' => '".$STUB["NAMEMODULE"].".', 'prefix' => '".($MODULE == "site" ? null : $MODULE)."', 'middleware' => ['Middleware".$STUB["NAMEMODULESTUDLY"]."']], function () {".PHP_EOL;
                if ($MODULE != "api"):
                    $CONSTANTROUTE .= "Route::get('', ['App\\Http\\Controllers\\".$STUB["NAMEMODULESTUDLY"]."\\Home', 'index'])->name('index');".PHP_EOL;
                endif;
                foreach ($TABLES as $TABLE) :
                    $STUB["NAMECLASS"]    = Str::studly($TABLE);
                    $STUB["NAMEROUTE"]    = Str::slug($TABLE);
                    $STUB["NAMEVARIABLE"] = Str::camel($TABLE);
                    $STUB["NAME"]         = $TABLE;
                    $CONSTANTROUTE        .= "Route::resource('".$STUB["NAMEROUTE"]."', 'App\\Http\\Controllers\\".$STUB["NAMEMODULESTUDLY"]."\\Base\\".$STUB["NAMECLASS"]."');".PHP_EOL;
                endforeach;
                $CONSTANTROUTE .= "});".PHP_EOL;
            endforeach;
            # -> AUTHORIZATIONS
            foreach (AppDoItEnum::$MODULES as $MODULE):
                $STUB["NAMEMODULESTUDLY"] = Str::studly($MODULE);
                $STUB["NAMEMODULE"]       = $MODULE;
                $CONSTANTROUTE            .= "Route::group(['as' => '".$STUB["NAMEMODULE"].".', 'prefix' => '".($MODULE == "site" ? null : $MODULE)."', 'middleware' => []], function () {".PHP_EOL;
                foreach (AppDoItEnum::$AUTHORIZATIONS as $AUTHORIZATION):
                    $STUB["NAMEROUTE"]    = Str::slug($AUTHORIZATION);
                    $STUB["NAMEVARIABLE"] = Str::camel($AUTHORIZATION);
                    $CONSTANTROUTE        .= "Route::get('".$STUB["NAMEROUTE"]."', ['App\\Http\\Controllers\\".$STUB["NAMEMODULESTUDLY"]."\\Authorize', '".$STUB["NAMEVARIABLE"]."'])->name('".$STUB["NAMEVARIABLE"]."');".PHP_EOL;
                    $CONSTANTROUTE        .= "Route::post('".$STUB["NAMEROUTE"]."', ['App\\Http\\Controllers\\".$STUB["NAMEMODULESTUDLY"]."\\Authorize', '".$STUB["NAMEVARIABLE"]."Do'])->name('".$STUB["NAMEVARIABLE"]."Do');".PHP_EOL;
                endforeach;
                $CONSTANTROUTE .= "});".PHP_EOL;
            endforeach;
            $CONSTANTROUTE .= "});".PHP_EOL;
            File::put("routes/core.php", $CONSTANTROUTE);
        }
    }
