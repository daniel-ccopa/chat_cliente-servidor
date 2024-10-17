<?php

namespace App\Controllers;

class Chat extends BaseController
{
    public function index()
    {
        return view('chat_view');
    }
}
