<?php
    namespace App\Http\Controllers\Dashboard\Core;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    class Coupon extends Controller {
        public function index() {
            return getView()->with($this->data);
        }
        public function create() {
            return getView()->with($this->data);
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
                session()->flash('validation', \Str::title($validator->errors()->first()));
                return redirect()->back()->withInput();
            }
            else {
                $coupon = Coupon();
                $coupon->fill($data);
                $coupon->save();
                session()->flash('success', trans("app.Successful"));
                if ($request->has("redirect")) {
                    return redirect()->to($request->get("redirect"));
                }
                else {
                    return redirect()->route("dashboard.coupon.edit", $coupon->id);
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
            if (method_exists(\Validation::class, "couponUpdate")) {
                $validator = \Validator::make($data, \Validation::couponUpdate($id)["rule"], \Validation::couponUpdate($id)["message"]);
            }
            else {
                $validator = \Validator::make($data, []);
            }
            if ($validator->fails()) {
                session()->flash('validation', \Str::title($validator->errors()->first()));
                return redirect()->back()->withInput();
            }
            else {
                $coupon = Coupon()->find($id);
                $coupon->fill($data);
                $coupon->save();
                session()->flash('success', trans("app.Successful"));
                if ($request->has("redirect")) {
                    return redirect()->to($request->get("redirect"));
                }
                else {
                    return redirect()->route("dashboard.coupon.edit", $id);
                }
            }
        }
        public function destroy($id) {
            $coupon = Coupon()->find($id);
            $coupon->delete();
            session()->flash('success', trans("app.Successful"));
            if (\request()->has("redirect")) {
                return redirect()->to(\request()->get("redirect"))->withInput();
            }
            else {
                return redirect()->route("dashboard.coupon.index")->withInput();
            }
        }
    }
