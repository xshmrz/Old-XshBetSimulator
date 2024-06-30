<?php
    namespace App\Models\Base;
    class User extends \App\Models\Core\User {
        protected static function boot() {
            parent::boot();
            static::retrieved(function (User $model) {});
            static::saving(function (User $model) {});
            static::deleting(function (User $model) {});
        }
    }
