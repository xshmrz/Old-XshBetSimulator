<?php
	namespace App\Models\Base;
	class CouponUpdate extends \App\Models\Core\CouponUpdate {
        protected static function boot() {
            parent::boot();
            static::retrieved(function (CouponUpdate $model) {});
            static::saving(function (CouponUpdate $model) {});
            static::deleting(function (CouponUpdate $model) {});
        }
	}
