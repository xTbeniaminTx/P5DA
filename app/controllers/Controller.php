<?php


class Controller
{
    //------------------------------------------------------------------------------------------------------------------
    //load Model
    public function model($model)
    {
        //Require model file
        require_once '../app/models/' . $model . '.php';

        //Instantiate model
        return new $model();
    }
    //------------------------------------------------------------------------------------------------------------------

}