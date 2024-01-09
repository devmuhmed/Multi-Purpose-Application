<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{

    public function index()
    {
        return Appointment::query()
            ->latest()
            ->paginate();
    }
}
