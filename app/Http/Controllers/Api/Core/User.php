<?php
    namespace App\Http\Controllers\Api\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Bjerke\ApiQueryBuilder\QueryBuilder as QueryBuilder;
    class User extends Controller {
        public function index() {
            $model        = User();
            $queryBuilder = new QueryBuilder($model, \request());
            $queryBuilder = $queryBuilder->build();
            if (\request()->has("pagination") && \request()->get("pagination") == "true") {
                if (\request()->has("per_page")) {
                    return responseOk(["message" => trans("app.Successful"), "data" => $queryBuilder->paginate(\request()->get("per_page"))->appends(\request()->except('page'))]);
                }
                else {
                    return responseOk(["message" => trans("app.Successful"), "data" => $queryBuilder->paginate()->appends(\request()->get('page'))]);
                }
            }
            else {
                return responseOk(["message" => trans("app.Successful"), "data" => $queryBuilder->get()]);
            }
        }
        public function store(Request $request) {
            $data = $request->all();
            if (method_exists(\Validation::class, "userStore")) {
                $validator = \Validator::make($data, \Validation::userStore()["rule"], \Validation::userStore()["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $user = User();
                $user->fill($data);
                $user->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $user->toArray()]);
            }
        }
        public function show($id) {
            $user = User()->find($id);
            if (empty($user)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            return responseOk(["message" => trans("app.Successful"), "data" => $user]);
        }
        public function update(Request $request, $id) {
            $data = $request->all();
            if (method_exists(\Validation::class, "userUpdate")) {
                $validator = \Validator::make($data, \Validation::userUpdate($id)["rule"], \Validation::userUpdate($id)["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $user = User()->find($id);
                if (empty($user)) {
                    return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
                }
                $user->fill($data);
                $user->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $user->toArray()]);
            }
        }
        public function destroy($id) {
            $user = User()->find($id);
            if (empty($user)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            $user->delete();
            return responseOk(["message" => trans("app.Successful"), "data" => $user->toArray()]);
        }
    }
