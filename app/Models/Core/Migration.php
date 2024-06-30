<?php
    /**
     * Created by Reliese Model.
     */
    namespace App\Models\Core;
    use Illuminate\Database\Eloquent\Model;
    /**
     * Class Migration
     * @property int|null $id
     * @property string|null $migration
     * @property int|null $batch
     * @package App\Models\Core
     * @method static \Illuminate\Database\Eloquent\Builder|Migration newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Migration newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Migration query()
     * @method static \Illuminate\Database\Eloquent\Builder|Migration whereBatch($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Migration whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Migration whereMigration($value)
     * @mixin \Eloquent
     */
    class Migration extends Model {
        protected     $table           = 'migration';
        public        $timestamps      = false;
        public static $snakeAttributes = false;
        protected $casts = [
            'batch' => 'int',
        ];
        protected $fillable = [
            'migration',
            'batch',
        ];
    }
