<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services\session;

class ProfileController extends BaseController
{
    public function index()
    {
        $session = session();
        $data = [
            'username' => $session->username,
            'role' => $session->role,
            'email' => $session->email,
            'loginTime' => $session->loginTime,
            'isLoggedIn' => $session->isLoggedin
        ];
        return view('v_profile', $data);
    }
}
