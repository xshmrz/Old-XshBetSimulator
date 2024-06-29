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
    class AppDoIt extends Command {
        protected $signature   = 'app:app-do-it';
        protected $description = 'Command Description';
        public function handle() {
            # -> CLEAN CONTROLLER
            foreach (AppDoItEnum::$MODULES as $MODULE):
                $STUB["NAMEMODULESTUDLY"] = Str::studly($MODULE);
                $STUB["NAMEMODULE"]       = $MODULE;
                $PATHCONTROLLERCORE       = "app/Http/Controllers/".$STUB["NAMEMODULESTUDLY"]."/Core";
                $PATHCONTROLLERBASE       = "app/Http/Controllers/".$STUB["NAMEMODULESTUDLY"]."/Base";
                File::cleanDirectory($PATHCONTROLLERCORE);
            endforeach;
            # -> CLEAN MODEL
            $PATHMODELCORE = "app/Models/Core";
            $PATHMODELBASE = "app/Models/Base";
            File::cleanDirectory($PATHMODELCORE);
            # -> UPLOAD AND STORAGE
            File::delete(\File::allFiles(base_path("storage")));   # -> Delete All Files In The Public Storage Directory
            File::delete(\File::allFiles(base_path("upload")));    # -> Delete All Files In The 'upload' Directory
            # -> COMMANDS
            $COMMANDS = [
                "migrate:fresh"         => ["--path" => "database/migrations/"],
                "code:models"           => null,
                "app:app-do-controller" => null,
                "app:app-do-model"      => null,
                "app:app-do-model-js"   => null,
                "app:app-do-model-scss" => null,
                "app:app-do-constant"   => null,
                "app:app-do-route"      => null,
                "app:app-do-translate"  => null,
                "ide-helper:generate"   => null,
                "ide-helper:meta"       => null,
                "ide-helper:models"     => ["--write" => "yes"],
                "db:seed"               => ["--class" => "DataSeeder"],
                "snapshot:create"       => ["name" => "snapshot_db"],
                "config:clear"          => null,
                "cache:clear"           => null,
                "route:clear"           => null,
                "view:clear"            => null,
            ];
            # -> CALL COMMANDS
            foreach ($COMMANDS as $COMMAND => $OPTION) :
                !is_null($OPTION) ? Artisan::call($COMMAND, $OPTION) : Artisan::call($COMMAND);
                $this->info($COMMAND);
            endforeach;
        }
    }
