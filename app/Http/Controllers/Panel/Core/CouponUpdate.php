<?php
    namespace App\Http\Controllers\Panel\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    class CouponUpdate extends Controller {
        public function index() {
            return getView()->with($this->data);
        }
        public function create() {
            return getView()->with($this->data);
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
                session()->flash('validation', \Str::title($validator->errors()->first()));
                return redirect()->back()->withInput();
            }
            else {
                $couponUpdate = CouponUpdate();
                $couponUpdate->fill($data);
                $couponUpdate->save();
                session()->flash('success', trans("app.Successful"));
                if ($request->has("redirect")) {
                    return redirect()->to($request->get("redirect"));
                }
                else {
                    return redirect()->route("panel.coupon-update.edit", $couponUpdate->id);
                }
            }
        }
        public function show($id) {
            $this->data["id"] = $id;
            return getView()->with($this->data);
        }
        public function edit($id) {
            $this->data["id"] = $id;
            return getView()->with($this->data);
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
                session()->flash('validation', \Str::title($validator->errors()->first()));
                return redirect()->back()->withInput();
            }
            else {
                $couponUpdate = CouponUpdate()->find($id);
                $couponUpdate->fill($data);
                $couponUpdate->save();
                session()->flash('success', trans("app.Successful"));
                if ($request->has("redirect")) {
                    return redirect()->to($request->get("redirect"));
                }
                else {
                    return redirect()->route("panel.coupon-update.edit", $id);
                }
            }
        }
        public function destroy($id) {
            $couponUpdate = CouponUpdate()->find($id);
            $couponUpdate->delete();
            session()->flash('success', trans("app.Successful"));
            if (\request()->has("redirect")) {
                return redirect()->to(\request()->get("redirect"))->withInput();
            }
            else {
                return redirect()->route("panel.coupon-update.index")->withInput();
            }
        }
    }
