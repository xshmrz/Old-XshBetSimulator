<?php
    /**
     * Created by Reliese Model.
     */
    namespace App\Models\Core;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    /**
     * Class User
     * @property int|null $id
     * @property string $username
     * @property string $email
     * @property string $password
     * @property string $firstname
     * @property string $lastname
     * @property Carbon $created_at
     * @property Carbon $updated_at
     * @property string $deleted_at
     * @package App\Models\Core
     * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|User query()
     * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstname($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereLastname($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
     * @mixin \Eloquent
     */
    class User extends Model {
        use SoftDeletes;
        protected     $table           = 'user';
        public static $snakeAttributes = false;
        protected $hidden = [
            'password',
        ];
        protected $fillable = [
            'username',
            'email',
            'password',
            'firstname',
            'lastname',
        ];
    }
