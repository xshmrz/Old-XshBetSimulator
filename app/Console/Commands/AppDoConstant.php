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
    class AppDoConstant extends Command {
        protected $signature   = 'app:app-do-constant';
        protected $description = 'Command Description';
        public function handle() {
            $CONSTANTHELPER = "<?php".PHP_EOL;
            $TABLES         = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            foreach ($TABLES as $TABLE) :
                $CONSTANTHELPER .= "defined('".$TABLE."') or define('".$TABLE."', '".$TABLE."');".PHP_EOL;
            endforeach;
            $COLUMNS = [];
            foreach ($TABLES as $TABLE) {
                foreach (Schema::getColumnListing($TABLE) as $COLUMN):
                    $COLUMNS[] = $COLUMN;
                endforeach;
            }
            $COLUMNS = array_unique($COLUMNS);
            foreach ($COLUMNS as $COLUMN) :
                $CONSTANTHELPER .= "defined('".$COLUMN."') or define('".$COLUMN."', '".$COLUMN."');".PHP_EOL;
            endforeach;
            $CONSTANTHELPER .= "\t# -> TABLE".PHP_EOL;
            foreach ($TABLES as $TABLE) {
                foreach (AppDoItEnum::$ELEMENTS as $ELEMENT):
                    $CONSTANTHELPER .= "defined('".Str::studly($TABLE).$ELEMENT."') or define('".Str::studly($TABLE).$ELEMENT."', '".Str::studly($TABLE).$ELEMENT."');".PHP_EOL;
                endforeach;
            }
            $CONSTANTHELPER .= "\t# -> AUTHORIZATION".PHP_EOL;
            foreach (AppDoItEnum::$AUTHORIZATIONS as $AUTHORIZATION):
                foreach (AppDoItEnum::$AUTHORIZATIONSELEMENTS as $ELEMENT):
                    $CONSTANTHELPER .= "defined('".Str::studly($AUTHORIZATION).$ELEMENT."') or define('".Str::studly($AUTHORIZATION).$ELEMENT."', '".Str::studly($AUTHORIZATION).$ELEMENT."');".PHP_EOL;
                endforeach;
            endforeach;
            File::put("app/App/Constant.php", $CONSTANTHELPER);
        }
    }
