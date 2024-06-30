<?php
    /**
     * Created by Reliese Model.
     */
    namespace App\Models\Core;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    /**
     * Class Coupon
     * @property int|null $id
     * @property string $no
     * @property int $status
     * @property string $data
     * @property float $odd
     * @property int $finish
     * @property int $live
     * @property Carbon $created_at
     * @property Carbon $updated_at
     * @property string $deleted_at
     * @package App\Models\Core
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereData($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereFinish($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereLive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereNo($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereOdd($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Coupon withoutTrashed()
     * @mixin \Eloquent
     */
    class Coupon extends Model {
        use SoftDeletes;
        protected     $table           = 'coupon';
        public static $snakeAttributes = false;
        protected $casts = [
            'status' => 'int',
            'odd'    => 'float',
            'finish' => 'int',
            'live'   => 'int',
        ];
        protected $fillable = [
            'no',
            'status',
            'data',
            'odd',
            'finish',
            'live',
        ];
    }
