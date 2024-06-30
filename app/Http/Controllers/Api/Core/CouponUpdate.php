<?php
    namespace App\Http\Controllers\Api\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Bjerke\ApiQueryBuilder\QueryBuilder as QueryBuilder;
    class CouponUpdate extends Controller {
        public function index() {
            $model          = CouponUpdate();
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
            if (method_exists(\Validation::class, "couponUpdateStore")) {
                $validator = \Validator::make($data, \Validation::couponUpdateStore()["rule"], \Validation::couponUpdateStore()["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $couponUpdate = CouponUpdate();
                $couponUpdate->fill($data);
                $couponUpdate->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $couponUpdate->toArray()]);
            }
        }
        public function show($id) {
            $couponUpdate = CouponUpdate()->find($id);
            if (empty($couponUpdate)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            return responseOk(["message" => trans("app.Successful"), "data" => $couponUpdate]);
        }
        public function update(Request $request, $id) {
            $data = $request->all();
            if (method_exists(\Validation::class, "couponUpdateUpdate")) {
                $validator = \Validator::make($data, \Validation::couponUpdateUpdate($id)["rule"], \Validation::couponUpdateUpdate($id)["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $couponUpdate = CouponUpdate()->find($id);
                if (empty($couponUpdate)) {
                    return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
                }
                $couponUpdate->fill($data);
                $couponUpdate->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $couponUpdate->toArray()]);
            }
        }
        public function destroy($id) {
            $couponUpdate = CouponUpdate()->find($id);
            if (empty($couponUpdate)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            $couponUpdate->delete();
            return responseOk(["message" => trans("app.Successful"), "data" => $couponUpdate->toArray()]);
        }
    }
