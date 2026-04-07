<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkLocation;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $this->authorize('view all attendances');
        $attendances = Attendance::with('employee.department')
            ->whereDate('attendance_date', Carbon::today())
            ->latest()
            ->paginate(20);
        return view('attendances.index', compact('attendances'));
    }

    public function my()
    {
        $user     = auth()->user();
        $employee = $user->employee;
        if (!$employee) {
            return redirect()->route('attendances.index')
                ->with('error', 'Akun Anda belum terhubung ke data karyawan.');
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);
        return view('attendances.my', compact('attendances', 'employee'));
    }

    public function checkInPage()
    {
        $user     = auth()->user();
        $employee = $user->employee;
        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda belum terhubung ke data karyawan. Hubungi HRD.');
        }

        $today           = Carbon::today();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $locations = WorkLocation::where('is_active', true)->get();
        $schedule  = WorkSchedule::where('is_default', true)->first();

        return view('attendances.checkin', compact('employee', 'todayAttendance', 'locations', 'schedule'));
    }

    public function validateLocation(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $locations = WorkLocation::where('is_active', true)->get();
        foreach ($locations as $location) {
            if ($location->isWithinRadius($request->latitude, $request->longitude)) {
                return response()->json([
                    'valid'       => true,
                    'location'    => $location->name,
                    'location_id' => $location->id,
                ]);
            }
        }
        return response()->json(['valid' => false, 'message' => 'Anda berada di luar radius kantor.']);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'photo'       => 'required|string',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'location_id' => 'required|exists:work_locations,id',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) {
            return response()->json(['valid' => false, 'message' => 'Akun tidak terhubung ke data karyawan.'], 403);
        }

        $today    = Carbon::today();
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        abort_if($existing && $existing->check_in, 400, 'Anda sudah melakukan check-in hari ini.');

        $photoPath = $this->saveBase64Photo($request->photo, 'checkin');

        $schedule    = WorkSchedule::where('is_default', true)->first();
        $now         = Carbon::now();
        $checkInEnd  = Carbon::parse($schedule->check_in_end);
        $lateMinutes = $now->gt($checkInEnd) ? $now->diffInMinutes($checkInEnd) : 0;
        $status      = $lateMinutes > 0 ? 'late' : 'present';

        Attendance::updateOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => $today],
            [
                'work_location_id'    => $request->location_id,
                'check_in'            => $now->format('H:i:s'),
                'check_in_photo'      => $photoPath,
                'check_in_latitude'   => $request->latitude,
                'check_in_longitude'  => $request->longitude,
                'check_in_address'    => $request->address ?? null,
                'check_in_ip'         => $request->ip(),
                'status'              => $status,
                'late_minutes'        => $lateMinutes,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Check-in berhasil!', 'time' => $now->format('H:i')]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'photo'     => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Akun tidak terhubung ke data karyawan.'], 403);
        }

        $today      = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        abort_if(!$attendance || !$attendance->check_in, 400, 'Anda belum melakukan check-in.');
        abort_if($attendance->check_out, 400, 'Anda sudah melakukan check-out hari ini.');

        $photoPath = $this->saveBase64Photo($request->photo, 'checkout');
        $now       = Carbon::now();

        $attendance->update([
            'check_out'            => $now->format('H:i:s'),
            'check_out_photo'      => $photoPath,
            'check_out_latitude'   => $request->latitude,
            'check_out_longitude'  => $request->longitude,
            'check_out_address'    => $request->address ?? null,
            'check_out_ip'         => $request->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'Check-out berhasil!', 'time' => $now->format('H:i')]);
    }

    public function todayStatus()
    {
        $employee = auth()->user()->employee;
        if (!$employee) return response()->json(['status' => 'no_employee']);

        $today = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        return response()->json([
            'checked_in'  => (bool)($today?->check_in),
            'checked_out' => (bool)($today?->check_out),
            'check_in'    => $today?->check_in,
            'check_out'   => $today?->check_out,
            'status'      => $today?->status,
        ]);
    }

    public function correctionForm(Attendance $attendance)
    {
        $this->authorize('correct attendance');
        return view('attendances.correction', compact('attendance'));
    }

    public function correct(Request $request, Attendance $attendance)
    {
        $this->authorize('correct attendance');
        $request->validate([
            'check_in'  => 'required',
            'check_out' => 'nullable',
            'status'    => 'required',
            'notes'     => 'required|string',
        ]);

        $attendance->update([
            'check_in'     => $request->check_in,
            'check_out'    => $request->check_out,
            'status'       => $request->status,
            'notes'        => $request->notes,
            'is_corrected' => true,
            'corrected_by' => auth()->id(),
        ]);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil dikoreksi.');
    }

    private function saveBase64Photo(string $base64, string $prefix): string
    {
        $image    = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
        $path     = 'attendances/' . date('Y/m/d') . '/' . $filename;
        Storage::disk('public')->put($path, $image);
        return $path;
    }
}
