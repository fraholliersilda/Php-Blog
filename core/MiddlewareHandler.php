<?php

namespace core;

class MiddlewareHandler{
    public static function run(array $middlewares){
        foreach($middlewares as $middleware){
            (new $middleware())->handle();
        }
    }
}