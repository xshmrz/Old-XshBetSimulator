<?php
    namespace App\Http\Controllers;
    class Authorize extends Controller {
        public function login() {
            return getView()->with($this->data);
        }
        public function loginDo() {
            $data      = request()->all();
            $validator = \Validator::make($data, \Validation::loginCheck()["rule"], \Validation::loginCheck()["message"]);
            if ($validator->fails()):
                if (request()->ajax()):
                    return responseUnprocessableEntity(["message" => \Str::title($validator->errors()->first())]);
                endif;
            endif;
            if ($data[MODULE] == DASHBOARD):
                $user = User()->where([email => $data[email], password => md5($data[password]), can_login_dashboard => \EnumYesNo::Yes])->first();
            endif;
            if ($data[MODULE] == PANEL):
                $user = User()->where([email => $data[email], password => md5($data[password]), can_login_panel => \EnumYesNo::Yes])->first();
            endif;
            if ($data[MODULE] == SITE):
                $user = User()->where([email => $data[email], password => md5($data[password])])->first();
            endif;
            if (empty($user)):
                if (request()->ajax()):
                    return responseNotFound(["message" => trans("app.User Not Found")]);
                endif;
            else:
                if (request()->ajax()):
                    Authorize()::sessionSet($user);
                    return responseOk(["message" => trans("app.Successful")]);
                endif;
            endif;
        }
        public function logout() {}
        public function logoutDo() {
            if (request()->ajax()):
                Authorize()::sessionRemove();
                return responseOk(["message" => trans("app.Successful")]);
            endif;
        }
    }
