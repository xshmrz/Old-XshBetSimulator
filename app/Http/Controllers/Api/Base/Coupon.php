<?php
    namespace App\Http\Controllers\Api\Base;
    use Illuminate\Http\Request;
    class Coupon extends \App\Http\Controllers\Api\Core\Coupon {
        public function index() {
            return parent::index();
        }
        public function store(Request $request) {
            return parent::store($request);
        }
        public function show($id) {
            return parent::show($id);
        }
        public function update(Request $request, $id) {
            return parent::update($request, $id);
        }
        public function destroy($id) {
            return parent::destroy($id);
        }
    }
