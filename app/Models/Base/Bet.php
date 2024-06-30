<?php
    namespace App\Models\Base;
    class Bet extends \App\Models\Core\Bet {
        protected static function boot() {
            parent::boot();
            static::retrieved(function (Bet $model) {});
            static::saving(function (Bet $model) {});
            static::deleting(function (Bet $model) {});
        }
    }
