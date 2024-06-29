<?php
	namespace App\Models\Base;
	class Coupon extends \App\Models\Core\Coupon {
        protected static function boot() {
            parent::boot();
            static::retrieved(function (Coupon $model) {});
            static::saving(function (Coupon $model) {});
            static::deleting(function (Coupon $model) {});
        }
	}
