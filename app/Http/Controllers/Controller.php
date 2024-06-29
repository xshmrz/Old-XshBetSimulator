<?php
    namespace App\Http\Controllers;
    use Illuminate\Routing\Controller as BaseController;
    class Controller extends BaseController {
        public $data;
        public function __construct() {
            $this->data = [];
        }
        public function index() {
            return getView();
        }
    }
