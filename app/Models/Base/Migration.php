<?php
    namespace App\Models\Base;
    class Migration extends \App\Models\Core\Migration {
        protected static function boot() {
            parent::boot();
            static::retrieved(function (Migration $model) {});
            static::saving(function (Migration $model) {});
            static::deleting(function (Migration $model) {});
        }
    }
