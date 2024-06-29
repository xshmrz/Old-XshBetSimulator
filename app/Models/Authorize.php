<?php
    namespace App\Models;
    use App\Models\Base\User;
    class Authorize extends User {
        const SESSION_USER = "session-user";
        # ->
        public function __construct(array $attributes = []) {
            parent::__construct($attributes);
            return $this->session();
        }
        # ->
        public function session() {
            if (session()->has(self::SESSION_USER)):
                $this->fillable[] = "id";
                $this->fill(session()->get(self::SESSION_USER)->toArray());
                return $this;
            else:
                return false;
            endif;
        }
        public static function sessionSet(User $user) {
            session()->put(self::SESSION_USER, $user);
        }
        public static function sessionRemove() {
            session()->remove(self::SESSION_USER);
        }
    }
