<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CouponUpdate
 *
 * @property int|null $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property string $status_update
 * @package App\Models\Core
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate whereStatusUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUpdate withoutTrashed()
 * @mixin \Eloquent
 */
class CouponUpdate extends Model
{
	use SoftDeletes;
	protected $table = 'coupon_update';
	public static $snakeAttributes = false;

	protected $fillable = [
		'status_update'
	];
}
