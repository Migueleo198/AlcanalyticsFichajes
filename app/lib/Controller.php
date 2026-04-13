<?php

class Controller
{
    public function load_model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model;
    }

    public function load_view($view, $datos = [])
    {
        $file = '../app/views/pages/' . $view . '.php';

        if (file_exists($file)) {

           
            if (is_array($datos)) {
                extract($datos);
            }

            require_once $file;

        } else {
            die("404 NOT FOUND");
        }
    }
}