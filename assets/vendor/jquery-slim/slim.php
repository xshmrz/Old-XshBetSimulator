<?php
    abstract class SlimStatus {
        const FAILURE = 'failure';
        const SUCCESS = 'success';
    }
    class Slim {
        public static function getImages($inputName = 'slim') {
            $values = Slim::getPostData($inputName);
            // test for errors
            if ($values === FALSE) {
                return FALSE;
            }
            // determine if contains multiple input values, if is singular, put in array
            $data = [];
            if (!is_array($values)) {
                $values = [$values];
            }
            // handle all posted fields
            foreach ($values as $value) {
                $inputValue = Slim::parseInput($value);
                if ($inputValue) {
                    array_push($data, $inputValue);
                }
            }
            // return the data collected from the fields
            return $data;
        }
        // $value should be in JSON format
        private static function parseInput($value) {
            // if no json received, exit, don't handle empty input values.
            if (empty($value)) {
                return NULL;
            }
            // If magic quotes enabled
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            // The data is posted as a JSON String so to be used it needs to be deserialized first
            $data = json_decode($value);
            // shortcut
            $input   = NULL;
            $actions = NULL;
            $output  = NULL;
            $meta    = NULL;
            if (isset ($data->input)) {
                $inputData = NULL;
                if (isset($data->input->image)) {
                    $inputData = Slim::getBase64Data($data->input->image);
                }
                else if (isset($data->input->field)) {
                    $filename = $_FILES[$data->input->field]['tmp_name'];
                    if ($filename) {
                        $inputData = file_get_contents($filename);
                    }
                }
                $input = [
                    'data'   => $inputData,
                    'name'   => $data->input->name,
                    'type'   => $data->input->type,
                    'size'   => $data->input->size,
                    'width'  => $data->input->width,
                    'height' => $data->input->height,
                ];
            }
            if (isset($data->output)) {
                $outputDate = NULL;
                if (isset($data->output->image)) {
                    $outputData = Slim::getBase64Data($data->output->image);
                }
                else if (isset ($data->output->field)) {
                    $filename = $_FILES[$data->output->field]['tmp_name'];
                    if ($filename) {
                        $outputData = file_get_contents($filename);
                    }
                }
                $output = [
                    'data'   => $outputData,
                    'name'   => $data->output->name,
                    'type'   => $data->output->type,
                    'width'  => $data->output->width,
                    'height' => $data->output->height,
                ];
            }
            if (isset($data->actions)) {
                $actions = [
                    'crop'     => $data->actions->crop ? [
                        'x'      => $data->actions->crop->x,
                        'y'      => $data->actions->crop->y,
                        'width'  => $data->actions->crop->width,
                        'height' => $data->actions->crop->height,
                        'type'   => $data->actions->crop->type,
                    ] : NULL,
                    'size'     => $data->actions->size ? [
                        'width'  => $data->actions->size->width,
                        'height' => $data->actions->size->height,
                    ] : NULL,
                    'rotation' => $data->actions->rotation,
                    'filters'  => $data->actions->filters ? [
                        'sharpen' => $data->actions->filters->sharpen,
                    ] : NULL,
                ];
            }
            if (isset($data->meta)) {
                $meta = $data->meta;
            }
            // We've sanitized the base64data and will now return the clean file object
            return [
                'input'   => $input,
                'output'  => $output,
                'actions' => $actions,
                'meta'    => $meta,
            ];
        }
        // $path should have trailing slash
        public static function saveFile($data, $name, $path = 'tmp/', $uid = TRUE) {
            // Add trailing slash if omitted
            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
            // Test if directory already exists
            if (!is_dir($path)) {
                mkdir($path, 0755, TRUE);
            }
            // Sanitize characters in file name
            $name = Slim::sanitizeFileName($name);
            // Let's put a unique id in front of the filename so we don't accidentally overwrite other files
            if ($uid) {
                $name = uniqid() . '_' . $name;
            }
            // Add name to path, we need the full path including the name to save the file
            $path = $path . $name;
            // store the file
            Slim::save($data, $path);
            // return the files new name and location
            return [
                'name' => $name,
                'path' => $path,
            ];
        }
        /**
         * Get data from remote URL
         * @param $url
         * @return string
         */
        public static function fetchURL($url, $maxFileSize) {
            if (!ini_get('allow_url_fopen')) {
                return NULL;
            }
            $content = NULL;
            try {
                $content = @file_get_contents($url, FALSE, NULL, 0, $maxFileSize);
            }
            catch (Exception $e) {
                return FALSE;
            }
            return $content;
        }
        public static function outputJSON($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        /**
         * http://stackoverflow.com/a/2021729
         * Remove anything which isn't a word, whitespace, number
         * or any of the following characters -_~,;[]().
         * If you don't need to handle multi-byte characters
         * you can use preg_replace rather than mb_ereg_replace
         * @param $str
         * @return string
         */
        public static function sanitizeFileName($str) {
            // Basic clean up
            $str = preg_replace('([^\w\s\d\-_~,;\[\]\(\).])', '', $str);
            // Remove any runs of periods
            $str = preg_replace('([\.]{2,})', '', $str);
            return $str;
        }
        /**
         * Gets the posted data from the POST or FILES object. If was using Slim to upload it will be in POST (as posted with hidden field) if not enhanced with Slim it'll be in FILES.
         * @param $inputName
         * @return array|bool
         */
        private static function getPostData($inputName) {
            $values = [];
            if (isset($_POST[$inputName])) {
                $values = $_POST[$inputName];
            }
            else if (isset($_FILES[$inputName])) {
                // Slim was not used to upload this file
                return FALSE;
            }
            return $values;
        }
        /**
         * Saves the data to a given location
         * @param $data
         * @param $path
         * @return bool
         */
        private static function save($data, $path) {
            if (!file_put_contents($path, $data)) {
                return FALSE;
            }
            return TRUE;
        }
        /**
         * Strips the "data:image..." part of the base64 data string so PHP can save the string as a file
         * @param $data
         * @return string
         */
        private static function getBase64Data($data) {
            return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
        }
    }