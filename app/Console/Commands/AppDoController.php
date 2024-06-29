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
    class AppDoController extends Command {
        protected $signature   = 'app:app-do-controller';
        protected $description = 'Command Description';
        public function handle() {
            $TABLES = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            # -> HOME
            foreach (AppDoItEnum::$MODULES as $MODULE):
                $STUB["NAMEMODULESTUDLY"] = Str::studly($MODULE);
                $STUB["NAMEMODULE"]       = $MODULE;
                if (!File::exists("app/Http/Controllers/".$MODULE."/Home.php")):
                    StubGenerator::from("/app/Console/Commands/ControllerHome.stub")
                                 ->to("app/Http/Controllers/".$MODULE)
                                 ->as("Home")
                                 ->ext("php")
                                 ->withReplacers($STUB)
                                 ->replace(true)
                                 ->save();
                endif;
            endforeach;
            # -> AUTHORIZE
            foreach (AppDoItEnum::$MODULES as $MODULE):
                $STUB["NAMEMODULESTUDLY"] = Str::studly($MODULE);
                $STUB["NAMEMODULE"]       = $MODULE;
                if (!File::exists("app/Http/Controllers/".$MODULE."/Authorize.php")):
                    StubGenerator::from("/app/Console/Commands/ControllerAuthorize.stub")
                                 ->to("app/Http/Controllers/".$MODULE)
                                 ->as("Authorize")
                                 ->ext("php")
                                 ->withReplacers($STUB)
                                 ->replace(true)
                                 ->save();
                endif;
            endforeach;
            # ->
            foreach (AppDoItEnum::$MODULES as $MODULE):
                $STUB["NAMEMODULESTUDLY"] = Str::studly($MODULE);
                $STUB["NAMEMODULE"]       = $MODULE;
                $PATHCONTROLLERCORE       = "app/Http/Controllers/".$STUB["NAMEMODULESTUDLY"]."/Core";
                $PATHCONTROLLERBASE       = "app/Http/Controllers/".$STUB["NAMEMODULESTUDLY"]."/Base";
                foreach ($TABLES as $TABLE) :
                    $STUB["NAMECLASS"]    = Str::studly($TABLE);
                    $STUB["NAMEROUTE"]    = Str::slug($TABLE);
                    $STUB["NAMEVARIABLE"] = Str::camel($TABLE);
                    $STUB["NAME"]         = $TABLE;
                    $STUBCORE             = "/app/Console/Commands/ControllerCore.stub";
                    $STUBBASE             = "/app/Console/Commands/ControllerBase.stub";
                    if ($MODULE == "api"):
                        $STUBCORE = "/app/Console/Commands/ControllerCoreApi.stub";
                        $STUBBASE = "/app/Console/Commands/ControllerBaseApi.stub";
                    endif;
                    StubGenerator::from($STUBCORE)
                                 ->to($PATHCONTROLLERCORE)
                                 ->as($STUB["NAMECLASS"])
                                 ->ext("php")
                                 ->withReplacers($STUB)
                                 ->replace(true)
                                 ->save();
                    if (!File::exists($PATHCONTROLLERBASE."/".$STUB["NAMECLASS"].".php")):
                        StubGenerator::from($STUBBASE)
                                     ->to($PATHCONTROLLERBASE)
                                     ->as($STUB["NAMECLASS"])
                                     ->ext("php")
                                     ->withReplacers($STUB)
                                     ->replace(true)
                                     ->save();
                    endif;
                endforeach;
                $DIFFCORE = array_map(fn($file) => $file->getFilename(), File::allFiles($PATHCONTROLLERCORE));
                $DIFFBASE = array_map(fn($file) => $file->getFilename(), File::allFiles($PATHCONTROLLERBASE));
                foreach (array_diff($DIFFBASE, $DIFFCORE) as $DELETE) {
                    $PATH = $PATHCONTROLLERBASE.DIRECTORY_SEPARATOR.$DELETE;
                    File::delete($PATH);
                }
            endforeach;
        }
    }
