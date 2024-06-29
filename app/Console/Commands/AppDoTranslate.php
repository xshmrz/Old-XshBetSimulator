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
    class AppDoTranslate extends Command {
        protected $signature   = 'app:app-do-translate';
        protected $description = 'Command Description';
        public function handle() {
            foreach (LaravelLocalization::getSupportedLanguagesKeys() as $LOCALE):
                if (!File::isDirectory("lang/".$LOCALE)):
                    File::makeDirectory("lang/".$LOCALE, 0777, true, true);
                endif;
                if (!File::exists("lang/".$LOCALE."/app.php")):
                    File::put("lang/".$LOCALE."/app.php", "<?php".PHP_EOL."return [];");
                endif;
            endforeach;
            # ->
            $DATA     = [];
            $FUNCTION = ['trans'];
            $PATH     = [app_path(), resource_path(), public_path()];
            $FILES    = new Finder\Finder();
            $FILES    = $FILES->in($PATH)->files();
            foreach ($FILES as $FILE) {
                if (preg_match_all("/[^\w|](".implode('|', $FUNCTION).")\([\'\"](([^\1)]+)+)[\'\"][\),]/siU", $FILE->getContents(), $MATCHES)) {
                    foreach ($MATCHES[2] as $KEY => $VALUE) {
                        $VALUE        = Str::replace(["app.", "enum."], "", $VALUE);
                        $DATA[$VALUE] = $VALUE;
                    }
                }
            }
            File::put("lang/app.php", "<?php".PHP_EOL."return ".var_export(array_keys($DATA), true).";");
            # ->
            foreach (LaravelLocalization::getSupportedLanguagesKeys() as $LOCALE) {
                $$LOCALE = include "lang/".$LOCALE."/app.php";
                foreach ($DATA as $KEY => $VALUE) {
                    if (array_key_exists($VALUE, $$LOCALE)) {
                        $DATALOCALE[$VALUE] = !empty($$LOCALE[$VALUE]) ? $$LOCALE[$VALUE] : Str::title($VALUE);
                    }
                    else {
                        $DATALOCALE[$VALUE] = Str::title($VALUE);
                    }
                }
                File::put("lang/{$LOCALE}/app.php", "<?php".PHP_EOL."return ".var_export($DATALOCALE, true).";");
            }
        }
    }
