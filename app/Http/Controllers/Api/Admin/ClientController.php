<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Client;

class ClientController
{

    public function index()
    {
        return Client::latest()->get();
    }
}
