<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'asd' => 'asdf'
        ];
        return view('v_home');
    }
}
