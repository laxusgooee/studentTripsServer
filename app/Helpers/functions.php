<?php

if (!function_exists('theme_config')) {
    /**
     * Get theme config
     *
     * @param  array $config Configuration
     * @param  string $name Configuration Name
     * @param  bool $default Configuration Default Value
     * @return any
     */
    function theme_config($config = [], $name, $default = true)
    {
        return isset($config[$name])? $config[$name]: $default;
    }
}

if (! function_exists('on_route')) {
    function on_route($route)
    {
        return Route::current() ? Request::is($route) : false;
    }
}

if (! function_exists('is_url')) {
    function is_url($string){
        return filter_var($string, FILTER_VALIDATE_URL)? true: false; 
    }
}

if (!function_exists('ajax_success')) {
    function ajax_success($data){

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }
}

if (!function_exists('ajax_error')) {
    function ajax_error($message){

        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
    }
}

    