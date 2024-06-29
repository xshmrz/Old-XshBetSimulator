<?php
    use Illuminate\Validation\Rule;
    class Validation {
        public function __construct() {}
        public static function loginCheck() {
            return [
                "rule"    => [
                    email    => "required|email",
                    password => "required",
                ],
                "message" => [],
            ];
        }
    }
