<?php
    namespace App\Http\Controllers\Api\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Bjerke\ApiQueryBuilder\QueryBuilder as QueryBuilder;
    class Migration extends Controller {
        public function index() {
            $model          = Migration();
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
            if (method_exists(\Validation::class, "migrationStore")) {
                $validator = \Validator::make($data, \Validation::migrationStore()["rule"], \Validation::migrationStore()["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $migration = Migration();
                $migration->fill($data);
                $migration->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $migration->toArray()]);
            }
        }
        public function show($id) {
            $migration = Migration()->find($id);
            if (empty($migration)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            return responseOk(["message" => trans("app.Successful"), "data" => $migration]);
        }
        public function update(Request $request, $id) {
            $data = $request->all();
            if (method_exists(\Validation::class, "migrationUpdate")) {
                $validator = \Validator::make($data, \Validation::migrationUpdate($id)["rule"], \Validation::migrationUpdate($id)["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $migration = Migration()->find($id);
                if (empty($migration)) {
                    return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
                }
                $migration->fill($data);
                $migration->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $migration->toArray()]);
            }
        }
        public function destroy($id) {
            $migration = Migration()->find($id);
            if (empty($migration)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            $migration->delete();
            return responseOk(["message" => trans("app.Successful"), "data" => $migration->toArray()]);
        }
    }
