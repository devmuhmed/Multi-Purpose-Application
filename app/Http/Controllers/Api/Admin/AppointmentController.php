<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{

    public function index()
    {
        return Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(request('status'),fn($query) => $query->where('status',AppointmentStatus::from(request('status'))))
            ->latest()
            ->paginate()
            ->through(fn($appointment) => [
                'id' => $appointment->id,
                'start_time' => $appointment->start_time->format('Y-m-d h:i A'),
                'end_time' => $appointment->end_time->format('Y-m-d h:i A'),
                'status' => [
                    'name' => $appointment->status->name,
                    'color' => $appointment->status->color(),
                ],
                'client' => $appointment->client,
            ]);
    }

    public function getStatusWithCount()
    {
        $cases = AppointmentStatus::cases();
        return collect($cases)->map(fn($status) => [
            'name' => $status->name,
            'value' => $status->value,
            'count' => Appointment::where('status', $status->value)->count(),
            'color' => AppointmentStatus::from($status->value)->color(),
        ]);
    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        Appointment::create([
            'title' => request('title'),
            'client_id' => 1,
            'start_time' => now(),
            'end_time' => now(),
            'description' => request('description'),
            'status' => AppointmentStatus::SCHEDULED,
        ]);

        return response()->json(['message' => 'success']);
    }
}
