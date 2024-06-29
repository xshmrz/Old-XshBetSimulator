<?php
    use BenSampo\Enum\Enum;
    class EnumBase extends Enum {
        public static function getAsSelect() {
            $data   = static::asArray();
            $select = [];
            foreach ($data as $value) {
                $select[$value] = static::getTranslation($value);
            }
            return $select;
        }
        public static function getTranslation($key) {
            return static::translations()[$key] ?? null;
        }
        public static function getColor($key) {
            return static::colors()[$key] ?? null;
        }
        protected static function translations() {
            return [];
        }
        protected static function colors() {
            return [];
        }
    }
    class EnumStatus extends EnumBase {
        const Active  = 1;
        const Passive = 2;
        protected static function translations() {
            return [
                self::Active  => trans("app.Active"),
                self::Passive => trans("app.Passive"),
            ];
        }
        protected static function colors() {
            return [
                self::Active  => "success",
                self::Passive => "danger",
            ];
        }
    }
    class EnumYesNo extends EnumBase {
        const Yes = 1;
        const No  = 2;
        protected static function translations() {
            return [
                self::Yes => trans("app.Yes"),
                self::No  => trans("app.No"),
            ];
        }
    }
    class EnumApproval extends EnumBase {
        const Waiting  = 1;
        const Approved = 2;
        const Denied   = 3;
        protected static function translations() {
            return [
                self::Waiting  => trans("app.Waiting"),
                self::Approved => trans("app.Approved"),
                self::Denied   => trans("app.Denied"),
            ];
        }
        protected static function colors() {
            return [
                self::Waiting  => "warning",
                self::Approved => "success",
                self::Denied   => "danger",
            ];
        }
    }
    class EnumUserRole extends EnumBase {
        const Root  = 1;
        const Admin = 2;
        const User  = 3;
        protected static function translations() {
            return [
                self::Root  => trans("app.Root"),
                self::Admin => trans("app.Admin"),
                self::User  => trans("app.User"),
            ];
        }
        protected static function colors() {
            return [
                self::Root  => "danger",
                self::Admin => "warning",
                self::User  => "primary",
            ];
        }
    }
    class EnumUserGender extends EnumBase {
        const Female                 = 1;
        const Male                   = 2;
        const I_Dont_Want_To_Specify = 3;
        protected static function translations() {
            return [
                self::Female                 => trans("app.Female"),
                self::Male                   => trans("app.Male"),
                self::I_Dont_Want_To_Specify => trans("app.I Do Not Want To Specify"),
            ];
        }
        protected static function colors() {
            return [
                self::Female                 => "",
                self::Male                   => "",
                self::I_Dont_Want_To_Specify => "",
            ];
        }
    }
    class EnumProjectAddedToCoupon extends EnumBase {
        const Yes = 1;
        const No  = 2;
        protected static function translations() {
            return [
                self::Yes => trans("app.Yes"),
                self::No  => trans("app.No"),
            ];
        }
        protected static function colors() {
            return [
                self::Yes => "",
                self::No  => "",
            ];
        }
    }
    class EnumProjectStatus extends EnumBase {
        const Pending = 1;
        const Win     = 2;
        const Lost    = 3;
        protected static function translations() {
            return [
                self::Pending => trans("app.Pending"),
                self::Win     => trans("app.Win"),
                self::Lost    => trans("app.Lost"),
            ];
        }
        protected static function colors() {
            return [
                self::Pending => "warning",
                self::Win     => "success",
                self::Lost    => "danger",
            ];
        }
    }

