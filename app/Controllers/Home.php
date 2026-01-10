<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Redirect to login page
        header('Location: /login.html');
        exit;
    }
}
