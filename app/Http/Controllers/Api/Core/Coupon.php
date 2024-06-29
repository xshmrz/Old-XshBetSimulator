<?php
    namespace App\Http\Controllers\Api\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Bjerke\ApiQueryBuilder\QueryBuilder as QueryBuilder;
    class Coupon extends Controller {
        public function index() {
            $model          = Coupon();
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
            if (method_exists(\Validation::class, "couponStore")) {
                $validator = \Validator::make($data, \Validation::couponStore()["rule"], \Validation::couponStore()["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $coupon = Coupon();
                $coupon->fill($data);
                $coupon->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $coupon->toArray()]);
            }
        }
        public function show($id) {
            $coupon = Coupon()->find($id);
            if (empty($coupon)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            return responseOk(["message" => trans("app.Successful"), "data" => $coupon]);
        }
        public function update(Request $request, $id) {
            $data = $request->all();
            if (method_exists(\Validation::class, "couponUpdate")) {
                $validator = \Validator::make($data, \Validation::couponUpdate($id)["rule"], \Validation::couponUpdate($id)["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                return responseUnprocessableEntity(["message" => $validator->errors()->first()]);
            }
            else {
                $coupon = Coupon()->find($id);
                if (empty($coupon)) {
                    return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
                }
                $coupon->fill($data);
                $coupon->save();
                return responseOk(["message" => trans("app.Successful"), "data" => $coupon->toArray()]);
            }
        }
        public function destroy($id) {
            $coupon = Coupon()->find($id);
            if (empty($coupon)) {
                return responseNotFound(["message" => trans("app.Not Found"), "data" => []]);
            }
            $coupon->delete();
            return responseOk(["message" => trans("app.Successful"), "data" => $coupon->toArray()]);
        }
    }
