<?php
    namespace App\Http\Controllers\Api\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Bjerke\ApiQueryBuilder\QueryBuilder as QueryBuilder;
    class Bet extends Controller {
        public function index() {
            $model          = Bet();
            $queryBuilder   = new QueryBuilder($model, \request());
            $queryBuilder   = $queryBuilder->build();
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
            if (method_exists(\Validation::class, "betStore")) {
                $validator = \Validator::make($data, \Validation::betStore()["rule"], \Validation::betStore()["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $bet = Bet();
                $bet->fill($data);
                $bet->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $bet->toArray()]);
            }
        }
        public function show($id) {
            $bet = Bet()->find($id);
            if (empty($bet)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            return responseOk(["message" => trans("app.Successful"), "data" => $bet]);
        }
        public function update(Request $request, $id) {
            $data = $request->all();
            if (method_exists(\Validation::class, "betUpdate")) {
                $validator = \Validator::make($data, \Validation::betUpdate($id)["rule"], \Validation::betUpdate($id)["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $bet = Bet()->find($id);
                if (empty($bet)) {
                    return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
                }
                $bet->fill($data);
                $bet->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $bet->toArray()]);
            }
        }
        public function destroy($id) {
            $bet = Bet()->find($id);
            if (empty($bet)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            $bet->delete();
            return responseOk(["message" => trans("app.Successful"), "data" => $bet->toArray()]);
        }
    }
