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
    class AppDoModelScss extends Command {
        protected $signature   = 'app:app-do-model-scss';
        protected $description = 'Command Description';
        public function handle() {
            $TABLES   = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $STUBFILE = "app/Console/Commands/ModelScss.stub";
            $DATA     = "";
            foreach ($TABLES as $TABLE) :
                $STUB["NAMECLASS"]    = Str::studly($TABLE);
                $STUB["NAMEROUTE"]    = Str::slug($TABLE);
                $STUB["NAMEVARIABLE"] = Str::camel($TABLE);
                $STUB["NAME"]         = $TABLE;
                $DATA                 .= StubGenerator::from($STUBFILE)
                                                      ->withReplacers($STUB)
                                                      ->replace(true)
                                                      ->toString().PHP_EOL;
            endforeach;
            File::put("assets/app.core.scss", $DATA);
        }
    }
