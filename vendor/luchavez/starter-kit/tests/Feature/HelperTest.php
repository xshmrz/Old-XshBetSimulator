<?php

use Illuminate\Support\Str;

use function Pest\Faker\faker;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

uses()->group('helpers');

/***** ENV FILE RELATED *****/

it('can update env variables', function (string $key, string $value, ?string $title, bool $override) {
    assertTrue(add_contents_to_env([$key => $value], $title, $override));

    $key = Str::of($key)->lower()->snake()->upper()->jsonSerialize();
    $find[] = get_combined_key_value($key, $value);

    if ($title) {
        $find[] = Str::of($title)->prepend('# ')->jsonSerialize();
    }

    assertTrue(Str::contains(file_get_contents(base_path('.env')), $find));

    // Todo: Add assertion for override
})->with([
    'with title' => [
        'key' => faker()->sentence(faker()->numberBetween(1, 2)),
        'value' => faker()->sentence(faker()->numberBetween(1, 3)),
        'title' => faker()->sentence,
        'override' => false,
    ],
    'without title' => [
        'key' => faker()->sentence(faker()->numberBetween(1, 2)),
        'value' => faker()->sentence(faker()->numberBetween(1, 3)),
        'title' => null,
        'override' => false,
    ],
    'with title and override to true' => [
        'key' => faker()->sentence(faker()->numberBetween(1, 2)),
        'value' => faker()->sentence(faker()->numberBetween(1, 3)),
        'title' => faker()->sentence,
        'override' => false,
    ],
    'without title and override to true' => [
        'key' => faker()->sentence(faker()->numberBetween(1, 2)),
        'value' => faker()->sentence(faker()->numberBetween(1, 3)),
        'title' => null,
        'override' => false,
    ],
])->group('env');

/***** DDD RELATED *****/

it('can decode domain to path', function (string $domain) {
    $str = null;
    foreach (explode('.', $domain) as $d) {
        $str .= '/domains/'.$d;
    }
    expect(domain_decode($domain))->toBe($str);
})->with('domains')->group('domain');

it('can decode domain to namespace', function (string $domain) {
    $str = null;
    foreach (explode('.', $domain) as $d) {
        $str .= '\\Domains\\'.$d;
    }
    expect(domain_decode($domain, true))->toBe($str);
})->with('domains')->group('domain');

it('can encode path to domain', function (string $input, ?string $expected) {
    expect(domain_encode($input))->toBe($expected);
})->with([
    'a path with no domain' => [
        'input' => '/var/www/html/app/Models/',
        'expected' => null,
    ],
    'a path with 1-level domain' => [
        'input' => '/var/www/html/domains/First/src/Models/',
        'expected' => 'First',
    ],
    'a path with 2-level domain' => [
        'input' => '/var/www/html/domains/First/domains/Second/src/Models/',
        'expected' => 'First.Second',
    ],
    'a path with 3-level domain' => [
        'input' => '/var/www/html/domains/First/domains/Second/domains/Third/src/Models/',
        'expected' => 'First.Second.Third',
    ],
])->group('domain');

it('can encode namespace to domain', function (string $input, ?string $expected) {
    expect(domain_encode($input))->toBe($expected);
})->with([
    'a namespace with no domain' => [
        'input' => 'App\\',
        'expected' => null,
    ],
    'a namespace with 1-level domain' => [
        'input' => 'Domains\\First\\Models\\',
        'expected' => 'First',
    ],
    'a namespace with 2-level domain' => [
        'input' => 'Domains\\First\\Domains\\Second\\Models\\',
        'expected' => 'First.Second',
    ],
    'a namespace with 3-level domain' => [
        'input' => 'Domains\\First\\Domains\\Second\\Domains\\Third\\Models\\',
        'expected' => 'First.Second.Third',
    ],
    'a package-based namespace with 1-level domain' => [
        'input' => 'Dummy\\Package\\Domains\\First\\Models\\',
        'expected' => 'First',
    ],
    'a package-based namespace with 2-level domain' => [
        'input' => 'Dummy\\Package\\Domains\\First\\Domains\\Second\\Models\\',
        'expected' => 'First.Second',
    ],
    'a package-based namespace with 3-level domain' => [
        'input' => 'Dummy\\Package\\Domains\\First\\Domains\\Second\\Domains\\Third\\Models\\',
        'expected' => 'First.Second.Third',
    ],
])->group('domain');

/***** APP CONFIG FILE RELATED *****/

it('can add and remove provider to app.php config file', function (string $provider) {
    assertTrue(add_provider_to_app_config($provider));
    assertFalse(add_provider_to_app_config($provider));
    assertTrue(remove_provider_from_app_config($provider));
    assertFalse(remove_provider_from_app_config($provider));
})
    ->with([
        'a 1-level domain' => 'Domains\\First\\Models\\Providers\\HelloServicerProvider::class',
        'a 2-level domain' => 'Domains\\First\\Domains\\Second\\Models\\Providers\\HelloServicerProvider::class',
        'a 3-level domain' => 'Domains\\First\\Domains\\Second\\Domains\\Third\\Models\\Providers\\HelloServicerProvider::class',
    ])
    ->group('app.config');
