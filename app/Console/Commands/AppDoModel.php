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
    class AppDoModel extends Command {
        protected $signature   = 'app:app-do-model';
        protected $description = 'Command Description';
        public function handle() {
            $TABLES        = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $STUBCORE      = "";
            $STUBBASE      = "app/Console/Commands/Model.stub";
            $PATHMODELCORE = "app/Models/Core";
            $PATHMODELBASE = "app/Models/Base";
            foreach ($TABLES as $TABLE) :
                $STUB["NAMECLASS"]    = Str::studly($TABLE);
                $STUB["NAMEROUTE"]    = Str::slug($TABLE);
                $STUB["NAMEVARIABLE"] = Str::camel($TABLE);
                $STUB["NAME"]         = $TABLE;
                if (!File::exists($PATHMODELBASE."/".$STUB["NAMECLASS"].".php")):
                    StubGenerator::from($STUBBASE)
                                 ->to($PATHMODELBASE)
                                 ->as($STUB["NAMECLASS"])
                                 ->ext("php")
                                 ->withReplacers($STUB)
                                 ->replace(true)
                                 ->save();
                endif;
            endforeach;
            $DIFFCORE = array_map(fn($FILE) => $FILE->getFilename(), File::allFiles($PATHMODELCORE));
            $DIFFBASE = array_map(fn($FILE) => $FILE->getFilename(), File::allFiles($PATHMODELBASE));
            foreach (array_diff($DIFFBASE, $DIFFCORE) as $DELETE) {
                $PATH = $PATHMODELBASE.DIRECTORY_SEPARATOR.$DELETE;
                File::delete($PATH);
            }
            $MODELHELPER = "<?php".PHP_EOL;
            foreach ($TABLES as $TABLE) :
                $STUB["NAMECLASS"]    = Str::studly($TABLE);
                $STUB["NAMEROUTE"]    = Str::slug($TABLE);
                $STUB["NAMEVARIABLE"] = Str::camel($TABLE);
                $MODELHELPER          .= "function ".$STUB["NAMECLASS"]."(){ return new \\App\Models\\Base\\".$STUB["NAMECLASS"]."(); }".PHP_EOL;
            endforeach;
            File::put("app/App/Model.php", $MODELHELPER);
        }
    }
