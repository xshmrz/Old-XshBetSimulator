<?php

use Luchavez\StarterKit\Enums\OnDeleteAction;
use Luchavez\StarterKit\Enums\OnUpdateAction;

return [
    'user_model' => env('SK_USER_MODEL', config('auth.providers.users.model')),
    'web_middleware' => env('SK_WEB_MIDDLEWARE', []),
    'api_middleware' => env('SK_API_MIDDLEWARE', []),
    'override_exception_handler' => env('SK_OVERRIDE_EXCEPTION_HANDLER', false),
    'enforce_morph_map' => env('SK_ENFORCE_MORPH_MAP', true),
    'verify_ssl' => env('SK_VERIFY_SSL', true),
    'change_locale' => [
        'enabled' => env('SK_CHANGE_LOCALE_ENABLED', true),
        'key' => env('SK_CHANGE_LOCALE_KEY', 'lang'),
    ],
    'columns' => [
        'expires' => [
            'column' => 'expires_at',
        ],
        'disables' => [
            'column' => 'disabled_at',
            'disabler_required' => env('SK_DISABLER_REQUIRED', false),
            'disabler_column' => 'disabler_id',
            'disable_reason_required' => env('SK_DISABLE_REASON_REQUIRED', false),
            'disable_reason_column' => 'disable_reason',
            'on_delete' => OnDeleteAction::SET_NULL,
            'on_update' => OnUpdateAction::CASCADE,
        ],
        'owned' => [
            'column' => 'owner_id',
            'on_delete' => OnDeleteAction::SET_NULL,
            'on_update' => OnUpdateAction::CASCADE,
        ],
        'usage' => [
            'column' => 'usage_left',
            'default' => null,
        ],
    ],
];
