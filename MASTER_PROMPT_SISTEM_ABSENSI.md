# MASTER PROMPT — Sistem Absensi, Bonus & Gaji
## Laravel 12 + Sneat Bootstrap Template (Local Development)

> **Cara pakai:** Berikan file ini ke AI coding assistant (Claude, Cursor, dll).
> Semua perintah terminal **dijalankan oleh Anda sendiri** — AI hanya menulis kode & instruksi.
> Jalankan setiap bagian secara berurutan dari atas ke bawah.

---

## KONTEKS PROYEK

Kamu adalah senior Laravel developer yang membantu membangun sistem manajemen SDM berbasis web.
Sistem ini mencakup: **absensi foto + GPS**, **pengajuan cuti & lembur**, **penggajian otomatis**, dan **manajemen bonus**.

### Stack
- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Sneat Admin Template (sudah ada)
- **Database:** MySQL (local)
- **Auth & Role:** Laravel Breeze + Spatie Laravel Permission
- **Queue:** Database queue driver
- **Storage:** Local storage (`storage/app/public`)
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/laravel-excel
- **Image:** intervention/image
- **Activity Log:** spatie/laravel-activitylog
- **Face Detection:** face-api.js (browser-side)
- **Notifikasi:** Laravel Notification (email/database)

### Aturan UI
- **Semua UI wajib menggunakan komponen Sneat** — card, table, badge, button, form, modal, dsb.
- **Warna ikuti tema Sneat** 
- **Hapus semua data dummy Sneat** — ganti dengan data sistem ini
- **Layout:** sidebar Sneat tetap dipakai, content area diisi halaman sistem ini
- **Jangan buat CSS custom baru** — gunakan utility class Sneat/Bootstrap yang ada

### Role
| Role | Slug |
|---|---|
| Super Admin | `super_admin` |
| HRD / Manager | `hrd` |
| Kepala Departemen | `kepala_departemen` |
| Karyawan | `karyawan` |

---

## BAGIAN 1 — PERSIAPAN AWAL

### 1.1 Prasyarat (jalankan di terminal Anda)

```bash
# Cek versi — pastikan semuanya tersedia
php --version       # harus >= 8.2
composer --version
node --version
yarn --version
mysql --version
```

### 1.2 Install project Laravel baru (skip jika sudah ada)

```bash
composer create-project laravel/laravel sistem-absensi
cd sistem-absensi
```

### 1.3 Install semua Composer package

```bash
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require spatie/laravel-medialibrary
composer require intervention/image
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
composer require laravel/sanctum
```

### 1.4 Install yarn package

```bash
yarn install face-api.js
yarn install sweetalert2
yarn install flatpickr
yarn install chart.js
```

### 1.5 Publish config package

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Intervention\Image\ImageServiceProvider"
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

### 1.6 Setup .env (sesuaikan dengan local Anda)

```env
APP_NAME="Sistem Absensi"
APP_ENV=local
APP_KEY=base64:cuL7XfxU66doNzZYReaTa9oLgrtbPasfpPIcIvZFSaA=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absenapp
DB_USERNAME=rudy
DB_PASSWORD=ganteng

QUEUE_CONNECTION=database

MAIL_MAILER=log        # pakai log dulu untuk local testing
MAIL_FROM_ADDRESS="noreply@absensi.local"
MAIL_FROM_NAME="Sistem Absensi"

FILESYSTEM_DISK=public
```

```bash
# Buat database di MySQL
mysql -u root -p -e "CREATE DATABASE absenapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Generate app key
php artisan key:generate
```

---

## BAGIAN 2 — DATABASE MIGRATIONS

> **Instruksi:** Buat semua file migration berikut di folder `database/migrations/`.
> Nama file sudah diurutkan dengan timestamp yang benar.

### 2.1 Migration: departments

**File:** `database/migrations/2024_01_01_000001_create_departments_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->unsignedBigInteger('head_employee_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
```

### 2.2 Migration: positions

**File:** `database/migrations/2024_01_01_000002_create_positions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
```

### 2.3 Migration: employees

**File:** `database/migrations/2024_01_01_000003_create_employees_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('nik', 16)->nullable()->unique();
            $table->string('photo')->nullable();
            $table->date('join_date');
            $table->date('resign_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->enum('gender', ['male', 'female']);
            $table->string('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
```

### 2.4 Migration: work_locations

**File:** `database/migrations/2024_01_01_000004_create_work_locations_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meters')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
```

### 2.5 Migration: work_schedules

**File:** `database/migrations/2024_01_01_000005_create_work_schedules_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('check_in_start');
            $table->time('check_in_end');
            $table->time('check_out_start');
            $table->time('check_out_end');
            $table->integer('work_hours')->default(8);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
```

### 2.6 Migration: attendances

**File:** `database/migrations/2024_01_01_000006_create_attendances_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->string('check_in_address')->nullable();
            $table->string('check_out_address')->nullable();
            $table->string('check_in_ip')->nullable();
            $table->string('check_out_ip')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'leave', 'holiday', 'permission'])->default('present');
            $table->decimal('late_minutes', 8, 2)->default(0);
            $table->decimal('early_minutes', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_corrected')->default(false);
            $table->foreignId('corrected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
```

### 2.7 Migration: leave_requests

**File:** `database/migrations/2024_01_01_000007_create_leave_requests_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('leave_type', ['annual', 'sick', 'maternity', 'paternity', 'emergency', 'permission', 'unpaid']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
```

### 2.8 Migration: overtime_requests

**File:** `database/migrations/2024_01_01_000008_create_overtime_requests_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('overtime_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('total_hours', 5, 2);
            $table->decimal('rate_multiplier', 4, 2)->default(1.5);
            $table->decimal('total_pay', 15, 2)->default(0);
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_requests');
    }
};
```

### 2.9 Migration: salary_components

**File:** `database/migrations/2024_01_01_000009_create_salary_components_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['allowance', 'deduction']);
            $table->enum('calculation_type', ['fixed', 'percentage']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
```

### 2.10 Migration: payrolls

**File:** `database/migrations/2024_01_01_000010_create_payrolls_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->integer('period_month');
            $table->integer('period_year');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('total_bonus', 15, 2)->default(0);
            $table->decimal('total_overtime', 15, 2)->default(0);
            $table->decimal('total_late_deduction', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_count')->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period_month', 'period_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
```

### 2.11 Migration: payroll_details

**File:** `database/migrations/2024_01_01_000011_create_payroll_details_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('salary_component_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->enum('type', ['allowance', 'deduction', 'bonus', 'overtime']);
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};
```

### 2.12 Migration: bonuses

**File:** `database/migrations/2024_01_01_000012_create_bonuses_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['performance', 'thr', 'project', 'referral', 'other']);
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('bonus_date');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
```

### 2.13 Jalankan semua migration

```bash
php artisan migrate
```

---

## BAGIAN 3 — MODELS

> **Instruksi:** Buat semua model berikut di folder `app/Models/`.
> Semua model menggunakan soft deletes kecuali yang tidak memerlukannya.

### 3.1 Model: Department

**File:** `app/Models/Department.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'head_employee_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function head()
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }
}
```

### 3.2 Model: Position

**File:** `app/Models/Position.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['department_id', 'name', 'base_salary', 'is_active'];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
```

### 3.3 Model: Employee

**File:** `app/Models/Employee.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'department_id', 'position_id', 'employee_code',
        'name', 'email', 'phone', 'nik', 'photo', 'join_date',
        'resign_date', 'status', 'gender', 'address',
        'bank_name', 'bank_account',
    ];

    protected $casts = [
        'join_date' => 'date',
        'resign_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('assets/img/avatars/default.png');
    }

    public function getMonthlySalaryAttribute(): float
    {
        return (float) $this->position->base_salary ?? 0;
    }
}
```

### 3.4 Model: WorkLocation

**File:** `app/Models/WorkLocation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkLocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude', 'radius_meters', 'is_active'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function isWithinRadius(float $lat, float $lng): bool
    {
        $earthRadius = 6371000; // meter
        $latDiff = deg2rad($lat - $this->latitude);
        $lngDiff = deg2rad($lng - $this->longitude);
        $a = sin($latDiff / 2) ** 2 +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($lngDiff / 2) ** 2;
        $distance = 2 * $earthRadius * asin(sqrt($a));
        return $distance <= $this->radius_meters;
    }
}
```

### 3.5 Model: Attendance

**File:** `app/Models/Attendance.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'work_location_id', 'attendance_date',
        'check_in', 'check_out',
        'check_in_photo', 'check_out_photo',
        'check_in_latitude', 'check_in_longitude',
        'check_out_latitude', 'check_out_longitude',
        'check_in_address', 'check_out_address',
        'check_in_ip', 'check_out_ip',
        'status', 'late_minutes', 'early_minutes',
        'notes', 'is_corrected', 'corrected_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_corrected' => 'boolean',
        'late_minutes' => 'decimal:2',
        'early_minutes' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workLocation()
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'present'    => '<span class="badge bg-label-success">Hadir</span>',
            'late'       => '<span class="badge bg-label-warning">Terlambat</span>',
            'absent'     => '<span class="badge bg-label-danger">Tidak Hadir</span>',
            'leave'      => '<span class="badge bg-label-info">Cuti</span>',
            'holiday'    => '<span class="badge bg-label-secondary">Libur</span>',
            'permission' => '<span class="badge bg-label-primary">Izin</span>',
            default      => '<span class="badge bg-label-secondary">-</span>',
        };
    }

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        return $this->check_in_photo ? asset('storage/' . $this->check_in_photo) : null;
    }

    public function getCheckOutPhotoUrlAttribute(): ?string
    {
        return $this->check_out_photo ? asset('storage/' . $this->check_out_photo) : null;
    }
}
```

### 3.6 Model: LeaveRequest

**File:** `app/Models/LeaveRequest.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'leave_type', 'start_date', 'end_date',
        'total_days', 'reason', 'attachment',
        'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return match ($this->leave_type) {
            'annual'     => 'Cuti Tahunan',
            'sick'       => 'Sakit',
            'maternity'  => 'Cuti Melahirkan',
            'paternity'  => 'Cuti Ayah',
            'emergency'  => 'Darurat',
            'permission' => 'Izin',
            'unpaid'     => 'Tanpa Gaji',
            default      => '-',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '<span class="badge bg-label-warning">Menunggu</span>',
            'approved' => '<span class="badge bg-label-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-label-danger">Ditolak</span>',
            default    => '<span class="badge bg-label-secondary">-</span>',
        };
    }
}
```

### 3.7 Model: OvertimeRequest

**File:** `app/Models/OvertimeRequest.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'overtime_date', 'start_time', 'end_time',
        'total_hours', 'rate_multiplier', 'total_pay', 'description',
        'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'approved_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'rate_multiplier' => 'decimal:2',
        'total_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculatePay(): float
    {
        $hourlyRate = $this->employee->position->base_salary / (22 * 8);
        return $hourlyRate * $this->total_hours * $this->rate_multiplier;
    }
}
```

### 3.8 Model: SalaryComponent

**File:** `app/Models/SalaryComponent.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'calculation_type',
        'amount', 'is_taxable', 'is_active', 'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];
}
```

### 3.9 Model: Payroll

**File:** `app/Models/Payroll.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'period_month', 'period_year',
        'base_salary', 'total_allowance', 'total_deduction',
        'total_bonus', 'total_overtime', 'total_late_deduction', 'net_salary',
        'working_days', 'present_days', 'absent_days', 'late_count',
        'status', 'approved_by', 'approved_at', 'paid_at', 'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'total_bonus' => 'decimal:2',
        'total_overtime' => 'decimal:2',
        'total_late_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return ($months[$this->period_month] ?? '-') . ' ' . $this->period_year;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'    => '<span class="badge bg-label-secondary">Draft</span>',
            'approved' => '<span class="badge bg-label-info">Disetujui</span>',
            'paid'     => '<span class="badge bg-label-success">Dibayar</span>',
            default    => '<span class="badge bg-label-secondary">-</span>',
        };
    }
}
```

### 3.10 Model: PayrollDetail

**File:** `app/Models/PayrollDetail.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id', 'salary_component_id', 'label', 'type', 'amount', 'notes',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function salaryComponent()
    {
        return $this->belongsTo(SalaryComponent::class);
    }
}
```

### 3.11 Model: Bonus

**File:** `app/Models/Bonus.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'payroll_id', 'type', 'title',
        'amount', 'bonus_date', 'description', 'status', 'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'performance' => 'Kinerja',
            'thr'         => 'THR',
            'project'     => 'Proyek',
            'referral'    => 'Referral',
            'other'       => 'Lainnya',
            default       => '-',
        };
    }
}
```

---

## BAGIAN 4 — SEEDERS

> **Instruksi:** Buat semua seeder berikut, lalu jalankan setelah selesai dibuat.

### 4.1 RolesAndPermissionsSeeder

**File:** `database/seeders/RolesAndPermissionsSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',
            // Karyawan
            'view employees', 'create employees', 'edit employees', 'delete employees',
            // Departemen
            'view departments', 'create departments', 'edit departments', 'delete departments',
            // Jabatan
            'view positions', 'create positions', 'edit positions', 'delete positions',
            // Absensi
            'view all attendances', 'view own attendance', 'create attendance',
            'edit attendance', 'correct attendance',
            // Cuti
            'view all leaves', 'view own leaves', 'create leave',
            'approve leave', 'reject leave',
            // Lembur
            'view all overtimes', 'view own overtimes', 'create overtime',
            'approve overtime', 'reject overtime',
            // Komponen Gaji
            'view salary components', 'create salary components',
            'edit salary components', 'delete salary components',
            // Payroll
            'view all payrolls', 'view own payroll', 'generate payroll',
            'approve payroll', 'mark payroll paid',
            // Bonus
            'view all bonuses', 'view own bonuses', 'create bonus',
            'approve bonus', 'delete bonus',
            // Laporan
            'view reports', 'export reports',
            // Pengaturan
            'view settings', 'edit settings',
            // Lokasi Kerja
            'view work locations', 'create work locations',
            'edit work locations', 'delete work locations',
            // Jadwal Kerja
            'view work schedules', 'create work schedules',
            'edit work schedules', 'delete work schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin — semua permission
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // HRD — hampir semua kecuali setting sistem
        $hrd = Role::firstOrCreate(['name' => 'hrd']);
        $hrd->syncPermissions([
            'view dashboard',
            'view employees', 'create employees', 'edit employees',
            'view departments', 'create departments', 'edit departments',
            'view positions', 'create positions', 'edit positions',
            'view all attendances', 'correct attendance',
            'view all leaves', 'approve leave', 'reject leave',
            'view all overtimes', 'approve overtime', 'reject overtime',
            'view salary components', 'create salary components', 'edit salary components',
            'view all payrolls', 'generate payroll', 'approve payroll', 'mark payroll paid',
            'view all bonuses', 'create bonus', 'approve bonus',
            'view reports', 'export reports',
            'view work locations', 'create work locations', 'edit work locations',
            'view work schedules', 'create work schedules', 'edit work schedules',
        ]);

        // Kepala Departemen
        $kepala = Role::firstOrCreate(['name' => 'kepala_departemen']);
        $kepala->syncPermissions([
            'view dashboard',
            'view employees',
            'view all attendances', 'view own attendance', 'create attendance',
            'view all leaves', 'view own leaves', 'create leave', 'approve leave', 'reject leave',
            'view all overtimes', 'view own overtimes', 'create overtime', 'approve overtime', 'reject overtime',
            'view own payroll',
            'view own bonuses',
            'view reports',
        ]);

        // Karyawan
        $karyawan = Role::firstOrCreate(['name' => 'karyawan']);
        $karyawan->syncPermissions([
            'view dashboard',
            'view own attendance', 'create attendance',
            'view own leaves', 'create leave',
            'view own overtimes', 'create overtime',
            'view own payroll',
            'view own bonuses',
        ]);
    }
}
```

### 4.2 MasterDataSeeder

**File:** `database/seeders/MasterDataSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use App\Models\WorkLocation;
use App\Models\WorkSchedule;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Departemen
        $dept = [
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Teknologi Informasi', 'code' => 'IT'],
            ['name' => 'Keuangan', 'code' => 'FIN'],
            ['name' => 'Operasional', 'code' => 'OPS'],
            ['name' => 'Marketing', 'code' => 'MKT'],
        ];
        foreach ($dept as $d) {
            Department::firstOrCreate(['code' => $d['code']], $d);
        }

        // Jabatan per departemen
        $positions = [
            ['HR', 'Manager HR', 12000000],
            ['HR', 'Staff HR', 6000000],
            ['IT', 'Manager IT', 15000000],
            ['IT', 'Senior Developer', 12000000],
            ['IT', 'Junior Developer', 7000000],
            ['FIN', 'Manager Keuangan', 13000000],
            ['FIN', 'Staff Akuntansi', 6500000],
            ['OPS', 'Supervisor Operasional', 9000000],
            ['OPS', 'Staff Operasional', 5500000],
            ['MKT', 'Manager Marketing', 12000000],
            ['MKT', 'Digital Marketing', 7000000],
        ];
        foreach ($positions as [$code, $name, $salary]) {
            $dept = Department::where('code', $code)->first();
            if ($dept) {
                Position::firstOrCreate(
                    ['department_id' => $dept->id, 'name' => $name],
                    ['base_salary' => $salary]
                );
            }
        }

        // Lokasi Kerja
        WorkLocation::firstOrCreate(
            ['name' => 'Kantor Pusat'],
            [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius_meters' => 100,
            ]
        );

        // Jadwal Kerja
        WorkSchedule::firstOrCreate(
            ['name' => 'Shift Normal'],
            [
                'check_in_start' => '07:30:00',
                'check_in_end' => '09:00:00',
                'check_out_start' => '17:00:00',
                'check_out_end' => '20:00:00',
                'work_hours' => 8,
                'is_default' => true,
            ]
        );

        // Komponen Gaji
        $components = [
            ['name' => 'Tunjangan Makan', 'type' => 'allowance', 'calculation_type' => 'fixed', 'amount' => 750000, 'is_taxable' => false],
            ['name' => 'Tunjangan Transport', 'type' => 'allowance', 'calculation_type' => 'fixed', 'amount' => 500000, 'is_taxable' => false],
            ['name' => 'Tunjangan Komunikasi', 'type' => 'allowance', 'calculation_type' => 'fixed', 'amount' => 300000, 'is_taxable' => false],
            ['name' => 'BPJS Kesehatan (4%)', 'type' => 'deduction', 'calculation_type' => 'percentage', 'amount' => 4, 'is_taxable' => false],
            ['name' => 'BPJS Ketenagakerjaan (2%)', 'type' => 'deduction', 'calculation_type' => 'percentage', 'amount' => 2, 'is_taxable' => false],
            ['name' => 'Potongan Keterlambatan', 'type' => 'deduction', 'calculation_type' => 'fixed', 'amount' => 0, 'is_taxable' => false],
        ];
        foreach ($components as $comp) {
            SalaryComponent::firstOrCreate(['name' => $comp['name']], $comp);
        }
    }
}
```

### 4.3 UserSeeder

**File:** `database/seeders/UserSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@absensi.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        // HRD
        $hrDept = Department::where('code', 'HR')->first();
        $hrPos = Position::where('name', 'Manager HR')->first();

        $hrdUser = User::firstOrCreate(
            ['email' => 'hrd@absensi.local'],
            ['name' => 'Budi HRD', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $hrdUser->assignRole('hrd');

        Employee::firstOrCreate(
            ['email' => 'hrd@absensi.local'],
            [
                'user_id' => $hrdUser->id,
                'department_id' => $hrDept?->id ?? 1,
                'position_id' => $hrPos?->id ?? 1,
                'employee_code' => 'EMP001',
                'name' => 'Budi HRD',
                'phone' => '08100000001',
                'join_date' => '2022-01-01',
                'status' => 'active',
                'gender' => 'male',
            ]
        );

        // Kepala Departemen IT
        $itDept = Department::where('code', 'IT')->first();
        $mgrPos = Position::where('name', 'Manager IT')->first();

        $kepalaUser = User::firstOrCreate(
            ['email' => 'kepala.it@absensi.local'],
            ['name' => 'Siti Kepala IT', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $kepalaUser->assignRole('kepala_departemen');

        Employee::firstOrCreate(
            ['email' => 'kepala.it@absensi.local'],
            [
                'user_id' => $kepalaUser->id,
                'department_id' => $itDept?->id ?? 2,
                'position_id' => $mgrPos?->id ?? 3,
                'employee_code' => 'EMP002',
                'name' => 'Siti Kepala IT',
                'phone' => '08100000002',
                'join_date' => '2022-03-01',
                'status' => 'active',
                'gender' => 'female',
            ]
        );

        // Karyawan biasa
        $devPos = Position::where('name', 'Junior Developer')->first();

        $karyawanUser = User::firstOrCreate(
            ['email' => 'karyawan@absensi.local'],
            ['name' => 'Andi Karyawan', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $karyawanUser->assignRole('karyawan');

        Employee::firstOrCreate(
            ['email' => 'karyawan@absensi.local'],
            [
                'user_id' => $karyawanUser->id,
                'department_id' => $itDept?->id ?? 2,
                'position_id' => $devPos?->id ?? 5,
                'employee_code' => 'EMP003',
                'name' => 'Andi Karyawan',
                'phone' => '08100000003',
                'join_date' => '2023-06-01',
                'status' => 'active',
                'gender' => 'male',
            ]
        );
    }
}
```

### 4.4 DatabaseSeeder

**File:** `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            MasterDataSeeder::class,
            UserSeeder::class,
        ]);
    }
}
```

### 4.5 Jalankan seeder

```bash
php artisan db:seed
```

---

## BAGIAN 5 — ROUTES

**File:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\SalaryComponentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\WorkLocationController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('employees', EmployeeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('work-locations', WorkLocationController::class);
    Route::resource('salary-components', SalaryComponentController::class);

    // Absensi
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/my', [AttendanceController::class, 'my'])->name('my');
        Route::get('/checkin', [AttendanceController::class, 'checkInPage'])->name('checkin');
        Route::post('/checkin', [AttendanceController::class, 'checkIn'])->name('checkin.store');
        Route::post('/checkout', [AttendanceController::class, 'checkOut'])->name('checkout.store');
        Route::get('/correction/{attendance}', [AttendanceController::class, 'correctionForm'])->name('correction');
        Route::put('/correction/{attendance}', [AttendanceController::class, 'correct'])->name('correction.update');
    });

    // Cuti
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::patch('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    // Lembur
    Route::resource('overtime-requests', OvertimeRequestController::class);
    Route::patch('overtime-requests/{overtimeRequest}/approve', [OvertimeRequestController::class, 'approve'])->name('overtime-requests.approve');
    Route::patch('overtime-requests/{overtimeRequest}/reject', [OvertimeRequestController::class, 'reject'])->name('overtime-requests.reject');

    // Payroll
    Route::prefix('payrolls')->name('payrolls.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/my', [PayrollController::class, 'my'])->name('my');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::patch('/{payroll}/approve', [PayrollController::class, 'approve'])->name('approve');
        Route::patch('/{payroll}/paid', [PayrollController::class, 'markPaid'])->name('paid');
        Route::get('/{payroll}/slip', [PayrollController::class, 'downloadSlip'])->name('slip');
    });

    // Bonus
    Route::resource('bonuses', BonusController::class);
    Route::patch('bonuses/{bonus}/approve', [BonusController::class, 'approve'])->name('bonuses.approve');

    // Laporan
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/payroll', [ReportController::class, 'payroll'])->name('payroll');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');
        Route::get('/payroll/export', [ReportController::class, 'exportPayroll'])->name('payroll.export');
    });

    // API untuk absensi (AJAX)
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/attendance/validate-location', [AttendanceController::class, 'validateLocation'])->name('attendance.validate-location');
        Route::get('/attendance/today-status', [AttendanceController::class, 'todayStatus'])->name('attendance.today-status');
    });
});

require __DIR__.'/auth.php';
```

---

## BAGIAN 6 — CONTROLLERS

### 6.1 DashboardController

**File:** `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $month = $today->month;
        $year = $today->year;

        if ($user->hasRole(['super_admin', 'hrd'])) {
            $data = [
                'total_employees' => Employee::where('status', 'active')->count(),
                'present_today'   => Attendance::whereDate('attendance_date', $today)->where('status', 'present')->count(),
                'late_today'      => Attendance::whereDate('attendance_date', $today)->where('status', 'late')->count(),
                'absent_today'    => Attendance::whereDate('attendance_date', $today)->where('status', 'absent')->count(),
                'pending_leaves'  => LeaveRequest::where('status', 'pending')->count(),
                'pending_overtime'=> OvertimeRequest::where('status', 'pending')->count(),
                'total_payrolls'  => Payroll::where('period_month', $month)->where('period_year', $year)->count(),
                'recent_attendances' => Attendance::with('employee')->whereDate('attendance_date', $today)->latest()->take(10)->get(),
                'chart_attendance' => $this->getAttendanceChartData($month, $year),
            ];
        } else {
            $employee = $user->employee ?? null;
            $todayAttendance = $employee
                ? Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->first()
                : null;

            $data = [
                'employee' => $employee,
                'today_attendance' => $todayAttendance,
                'monthly_present' => $employee ? Attendance::where('employee_id', $employee->id)
                    ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)
                    ->whereIn('status', ['present', 'late'])->count() : 0,
                'monthly_late' => $employee ? Attendance::where('employee_id', $employee->id)
                    ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)
                    ->where('status', 'late')->count() : 0,
                'pending_leaves' => $employee ? LeaveRequest::where('employee_id', $employee->id)->where('status', 'pending')->count() : 0,
                'latest_payroll' => $employee ? Payroll::where('employee_id', $employee->id)->latest()->first() : null,
            ];
        }

        return view('dashboard', $data);
    }

    private function getAttendanceChartData(int $month, int $year): array
    {
        $days = Carbon::create($year, $month)->daysInMonth;
        $labels = [];
        $present = [];
        $late = [];

        for ($d = 1; $d <= min($days, Carbon::today()->day); $d++) {
            $date = Carbon::create($year, $month, $d);
            $labels[] = $d;
            $present[] = Attendance::whereDate('attendance_date', $date)->where('status', 'present')->count();
            $late[] = Attendance::whereDate('attendance_date', $date)->where('status', 'late')->count();
        }

        return compact('labels', 'present', 'late');
    }
}
```

### 6.2 AttendanceController

**File:** `app/Http/Controllers/AttendanceController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkLocation;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
        $user = auth()->user();
        $employee = $user->employee;
        abort_if(!$employee, 403);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);
        return view('attendances.my', compact('attendances', 'employee'));
    }

    public function checkInPage()
    {
        $user = auth()->user();
        $employee = $user->employee;
        abort_if(!$employee, 403);

        $today = Carbon::today();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $locations = WorkLocation::where('is_active', true)->get();
        $schedule = WorkSchedule::where('is_default', true)->first();

        return view('attendances.checkin', compact('employee', 'todayAttendance', 'locations', 'schedule'));
    }

    public function validateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $locations = WorkLocation::where('is_active', true)->get();
        foreach ($locations as $location) {
            if ($location->isWithinRadius($request->latitude, $request->longitude)) {
                return response()->json([
                    'valid' => true,
                    'location' => $location->name,
                    'location_id' => $location->id,
                ]);
            }
        }
        return response()->json(['valid' => false, 'message' => 'Anda berada di luar radius kantor.']);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'photo'        => 'required|string',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'location_id'  => 'required|exists:work_locations,id',
        ]);

        $employee = auth()->user()->employee;
        abort_if(!$employee, 403);

        $today = Carbon::today();
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        abort_if($existing && $existing->check_in, 400, 'Anda sudah melakukan check-in hari ini.');

        // Simpan foto
        $photoPath = $this->saveBase64Photo($request->photo, 'checkin');

        // Hitung keterlambatan
        $schedule = WorkSchedule::where('is_default', true)->first();
        $now = Carbon::now();
        $checkInEnd = Carbon::parse($schedule->check_in_end);
        $lateMinutes = $now->gt($checkInEnd) ? $now->diffInMinutes($checkInEnd) : 0;
        $status = $lateMinutes > 0 ? 'late' : 'present';

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => $today],
            [
                'work_location_id'   => $request->location_id,
                'check_in'           => $now->format('H:i:s'),
                'check_in_photo'     => $photoPath,
                'check_in_latitude'  => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in_address'   => $request->address ?? null,
                'check_in_ip'        => $request->ip(),
                'status'             => $status,
                'late_minutes'       => $lateMinutes,
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
        abort_if(!$employee, 403);

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        abort_if(!$attendance || !$attendance->check_in, 400, 'Anda belum melakukan check-in.');
        abort_if($attendance->check_out, 400, 'Anda sudah melakukan check-out hari ini.');

        $photoPath = $this->saveBase64Photo($request->photo, 'checkout');
        $now = Carbon::now();

        $attendance->update([
            'check_out'           => $now->format('H:i:s'),
            'check_out_photo'     => $photoPath,
            'check_out_latitude'  => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_address'   => $request->address ?? null,
            'check_out_ip'        => $request->ip(),
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
        $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
        $path = 'attendances/' . date('Y/m/d') . '/' . $filename;
        Storage::disk('public')->put($path, $image);
        return $path;
    }
}
```

### 6.3 PayrollController (core engine)

**File:** `app/Http/Controllers/PayrollController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Bonus;
use App\Models\OvertimeRequest;
use App\Models\SalaryComponent;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index()
    {
        $this->authorize('view all payrolls');
        $month = request('month', Carbon::now()->month);
        $year  = request('year', Carbon::now()->year);

        $payrolls = Payroll::with('employee.department')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->paginate(20);

        return view('payrolls.index', compact('payrolls', 'month', 'year'));
    }

    public function my()
    {
        $employee = auth()->user()->employee;
        abort_if(!$employee, 403);
        $payrolls = Payroll::where('employee_id', $employee->id)->latest()->paginate(12);
        return view('payrolls.my', compact('payrolls', 'employee'));
    }

    public function generate(Request $request)
    {
        $this->authorize('generate payroll');
        $request->validate([
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer',
            'employee_ids'=> 'required|array',
        ]);

        $generated = 0;
        foreach ($request->employee_ids as $empId) {
            $employee = Employee::find($empId);
            if (!$employee) continue;

            $payroll = $this->generateForEmployee($employee, $request->month, $request->year);
            if ($payroll) $generated++;
        }

        return response()->json(['success' => true, 'generated' => $generated]);
    }

    private function generateForEmployee(Employee $employee, int $month, int $year): ?Payroll
    {
        // Cek duplikat
        $existing = Payroll::where('employee_id', $employee->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();
        if ($existing) return $existing;

        $schedule = WorkSchedule::where('is_default', true)->first();
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        // Hitung hari kerja (Senin-Jumat)
        $workingDays = 0;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $day = Carbon::create($year, $month, $d)->dayOfWeek;
            if ($day >= 1 && $day <= 5) $workingDays++;
        }

        // Rekap absensi bulan ini
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $presentDays = $attendances->whereIn('status', ['present', 'late'])->count();
        $absentDays  = max(0, $workingDays - $presentDays - $attendances->where('status', 'leave')->count());
        $lateCount   = $attendances->where('status', 'late')->count();
        $lateMinutes = $attendances->sum('late_minutes');

        // Gaji pokok
        $baseSalary = (float) $employee->position->base_salary;
        $dailyRate  = $baseSalary / $workingDays;
        $hourlyRate = $baseSalary / ($workingDays * 8);

        // Komponen tunjangan
        $components = SalaryComponent::where('is_active', true)->get();
        $totalAllowance = 0;
        $totalDeduction = 0;
        $details = [];

        foreach ($components as $comp) {
            $amount = $comp->calculation_type === 'fixed'
                ? $comp->amount
                : ($baseSalary * $comp->amount / 100);

            if ($comp->type === 'allowance') {
                $totalAllowance += $amount;
                $details[] = ['label' => $comp->name, 'type' => 'allowance', 'amount' => $amount, 'salary_component_id' => $comp->id];
            } else {
                $totalDeduction += $amount;
                $details[] = ['label' => $comp->name, 'type' => 'deduction', 'amount' => $amount, 'salary_component_id' => $comp->id];
            }
        }

        // Potongan keterlambatan (Rp5.000 per menit, max 10%)
        $lateDeduction = min($lateMinutes * 5000, $baseSalary * 0.1);

        // Potongan absen
        $absentDeduction = $absentDays * $dailyRate;
        $totalDeduction += $lateDeduction + $absentDeduction;

        // Bonus bulan ini
        $bonuses = Bonus::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereMonth('bonus_date', $month)
            ->whereYear('bonus_date', $year)
            ->whereNull('payroll_id')
            ->get();
        $totalBonus = $bonuses->sum('amount');

        // Lembur bulan ini
        $overtimes = OvertimeRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereMonth('overtime_date', $month)
            ->whereYear('overtime_date', $year)
            ->get();
        $totalOvertime = $overtimes->sum('total_pay');

        // Net gaji
        $netSalary = $baseSalary + $totalAllowance + $totalBonus + $totalOvertime - $totalDeduction;

        // Buat payroll
        $payroll = Payroll::create([
            'employee_id'        => $employee->id,
            'period_month'       => $month,
            'period_year'        => $year,
            'base_salary'        => $baseSalary,
            'total_allowance'    => $totalAllowance,
            'total_deduction'    => $totalDeduction,
            'total_bonus'        => $totalBonus,
            'total_overtime'     => $totalOvertime,
            'total_late_deduction'=> $lateDeduction,
            'net_salary'         => $netSalary,
            'working_days'       => $workingDays,
            'present_days'       => $presentDays,
            'absent_days'        => $absentDays,
            'late_count'         => $lateCount,
        ]);

        // Simpan detail
        foreach ($details as $detail) {
            $payroll->details()->create($detail);
        }

        // Tambahkan bonus ke detail
        foreach ($bonuses as $bonus) {
            $payroll->details()->create(['label' => $bonus->title, 'type' => 'bonus', 'amount' => $bonus->amount]);
            $bonus->update(['payroll_id' => $payroll->id, 'status' => 'paid']);
        }

        // Tambahkan lembur ke detail
        foreach ($overtimes as $overtime) {
            $payroll->details()->create([
                'label' => 'Lembur ' . $overtime->overtime_date->format('d M'),
                'type' => 'overtime',
                'amount' => $overtime->total_pay,
            ]);
        }

        return $payroll;
    }

    public function show(Payroll $payroll)
    {
        $this->authorize($payroll->employee->user_id === auth()->id() ? 'view own payroll' : 'view all payrolls');
        $payroll->load('employee.department', 'employee.position', 'details');
        return view('payrolls.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        $this->authorize('approve payroll');
        $payroll->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Payroll disetujui.');
    }

    public function markPaid(Payroll $payroll)
    {
        $this->authorize('mark payroll paid');
        $payroll->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Payroll ditandai sudah dibayar.');
    }

    public function downloadSlip(Payroll $payroll)
    {
        $payroll->load('employee.department', 'employee.position', 'details');
        $pdf = Pdf::loadView('payrolls.slip-pdf', compact('payroll'));
        return $pdf->download('slip-gaji-' . $payroll->employee->employee_code . '-' . $payroll->period_label . '.pdf');
    }
}
```

---

## BAGIAN 7 — VIEWS (BLADE)

> **Instruksi:** Semua view menggunakan layout Sneat. Hapus semua halaman demo/sample Sneat.
> Buat folder sesuai struktur di bawah. Gunakan komponen Bootstrap/Sneat sepenuhnya.

### 7.1 Layout utama

**File:** `resources/views/layouts/app.blade.php`

> Layout ini adalah wrapper Sneat. Hapus menu-menu demo Sneat, ganti dengan menu sistem ini:
> - Dashboard
> - Master Data (Karyawan, Departemen, Jabatan, Lokasi Kerja, Komponen Gaji)
> - Absensi (Semua Absensi, Absensi Saya, Check-in/out)
> - Cuti & Lembur
> - Penggajian (Payroll, Slip Gaji Saya)
> - Bonus
> - Laporan
> - Pengaturan

```blade
<!doctype html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Sistem Absensi') — {{ config('app.name') }}</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  @stack('styles')
  <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Sidebar -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <svg width="25" height="25" viewBox="0 0 25 25" fill="none"><rect width="25" height="25" rx="5" fill="#696cff"/><text x="5" y="19" font-size="14" fill="white" font-weight="bold">A</text></svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">AbsensiApp</span>
          </a>
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>
        <div class="menu-inner-shadow"></div>
        <ul class="menu-inner py-1">

          <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div>Dashboard</div>
            </a>
          </li>

          @canany(['view employees','view departments','view positions','view work locations','view salary components'])
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
          </li>
          @can('view employees')
          <li class="menu-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <a href="{{ route('employees.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-group"></i>
              <div>Karyawan</div>
            </a>
          </li>
          @endcan
          @can('view departments')
          <li class="menu-item {{ request()->routeIs('departments.*') ? 'active' : '' }}">
            <a href="{{ route('departments.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-building"></i>
              <div>Departemen</div>
            </a>
          </li>
          @endcan
          @can('view positions')
          <li class="menu-item {{ request()->routeIs('positions.*') ? 'active' : '' }}">
            <a href="{{ route('positions.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-badge"></i>
              <div>Jabatan</div>
            </a>
          </li>
          @endcan
          @can('view work locations')
          <li class="menu-item {{ request()->routeIs('work-locations.*') ? 'active' : '' }}">
            <a href="{{ route('work-locations.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-map"></i>
              <div>Lokasi Kerja</div>
            </a>
          </li>
          @endcan
          @can('view salary components')
          <li class="menu-item {{ request()->routeIs('salary-components.*') ? 'active' : '' }}">
            <a href="{{ route('salary-components.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-money"></i>
              <div>Komponen Gaji</div>
            </a>
          </li>
          @endcan
          @endcanany

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Absensi</span>
          </li>
          @can('view all attendances')
          <li class="menu-item {{ request()->routeIs('attendances.index') ? 'active' : '' }}">
            <a href="{{ route('attendances.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-calendar-check"></i>
              <div>Semua Absensi</div>
            </a>
          </li>
          @endcan
          @can('view own attendance')
          <li class="menu-item {{ request()->routeIs('attendances.checkin') ? 'active' : '' }}">
            <a href="{{ route('attendances.checkin') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-camera"></i>
              <div>Check-in / Check-out</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('attendances.my') ? 'active' : '' }}">
            <a href="{{ route('attendances.my') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-list-check"></i>
              <div>Absensi Saya</div>
            </a>
          </li>
          @endcan

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Cuti & Lembur</span>
          </li>
          @canany(['view all leaves','view own leaves'])
          <li class="menu-item {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
            <a href="{{ route('leave-requests.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-calendar-x"></i>
              <div>Pengajuan Cuti</div>
            </a>
          </li>
          @endcanany
          @canany(['view all overtimes','view own overtimes'])
          <li class="menu-item {{ request()->routeIs('overtime-requests.*') ? 'active' : '' }}">
            <a href="{{ route('overtime-requests.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-time"></i>
              <div>Pengajuan Lembur</div>
            </a>
          </li>
          @endcanany

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Penggajian</span>
          </li>
          @can('view all payrolls')
          <li class="menu-item {{ request()->routeIs('payrolls.index') ? 'active' : '' }}">
            <a href="{{ route('payrolls.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-wallet"></i>
              <div>Kelola Payroll</div>
            </a>
          </li>
          @endcan
          @can('view own payroll')
          <li class="menu-item {{ request()->routeIs('payrolls.my') ? 'active' : '' }}">
            <a href="{{ route('payrolls.my') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-receipt"></i>
              <div>Slip Gaji Saya</div>
            </a>
          </li>
          @endcan
          @canany(['view all bonuses','view own bonuses'])
          <li class="menu-item {{ request()->routeIs('bonuses.*') ? 'active' : '' }}">
            <a href="{{ route('bonuses.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-gift"></i>
              <div>Bonus</div>
            </a>
          </li>
          @endcanany

          @can('view reports')
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Laporan</span>
          </li>
          <li class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a href="{{ route('reports.attendance') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
              <div>Laporan Absensi</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="{{ route('reports.payroll') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
              <div>Laporan Gaji</div>
            </a>
          </li>
          @endcan

        </ul>
      </aside>
      <!-- / Sidebar -->

      <div class="layout-page">
        <!-- Navbar -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>
          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <div class="navbar-nav align-items-center">
              <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Cari..." />
              </div>
            </div>
            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                          <small class="text-muted">{{ auth()->user()->getRoleNames()->first() }}</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li><div class="dropdown-divider"></div></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item">
                        <i class="bx bx-power-off me-2"></i>
                        <span>Keluar</span>
                      </button>
                    </form>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
        <!-- / Navbar -->

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            {{-- Flash messages --}}
            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @yield('content')
          </div>
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">© {{ date('Y') }} <strong>AbsensiApp</strong> — Sistem Manajemen SDM</div>
            </div>
          </footer>
        </div>
      </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
```

### 7.2 Halaman Check-in / Check-out

**File:** `resources/views/attendances/checkin.blade.php`

```blade
@extends('layouts.app')
@section('title', 'Check-in / Check-out')
@push('styles')
<style>
  #video-preview { width: 100%; border-radius: 12px; background: #000; }
  #canvas-preview { display: none; }
  .gps-badge { font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">

    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Check-in / Check-out</h5>
        <span class="badge bg-label-primary" id="current-time">{{ now()->format('H:i:s') }}</span>
      </div>
      <div class="card-body">

        {{-- Status hari ini --}}
        @if($todayAttendance)
          <div class="row mb-4">
            <div class="col-6">
              <div class="d-flex align-items-center p-3 rounded bg-label-success">
                <i class="bx bx-log-in fs-3 me-3 text-success"></i>
                <div>
                  <div class="text-muted small">Check-in</div>
                  <div class="fw-bold">{{ $todayAttendance->check_in ?? '-' }}</div>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 rounded {{ $todayAttendance->check_out ? 'bg-label-info' : 'bg-label-secondary' }}">
                <i class="bx bx-log-out fs-3 me-3"></i>
                <div>
                  <div class="text-muted small">Check-out</div>
                  <div class="fw-bold">{{ $todayAttendance->check_out ?? 'Belum' }}</div>
                </div>
              </div>
            </div>
          </div>
        @endif

        @if(!$todayAttendance || !$todayAttendance->check_out)
          {{-- Kamera --}}
          <div class="mb-3">
            <video id="video-preview" autoplay playsinline></video>
            <canvas id="canvas-preview"></canvas>
          </div>

          {{-- Status GPS --}}
          <div class="mb-3 d-flex align-items-center gap-2">
            <span class="badge bg-label-warning gps-badge" id="gps-status">
              <i class="bx bx-map-pin"></i> Mendeteksi lokasi...
            </span>
            <small class="text-muted" id="gps-address"></small>
          </div>

          {{-- Tombol --}}
          @if(!$todayAttendance)
            <button class="btn btn-primary w-100 btn-lg" id="btn-checkin" disabled>
              <i class="bx bx-camera me-2"></i> Check-in Sekarang
            </button>
          @elseif(!$todayAttendance->check_out)
            <button class="btn btn-warning w-100 btn-lg" id="btn-checkout" disabled>
              <i class="bx bx-log-out me-2"></i> Check-out Sekarang
            </button>
          @endif
        @else
          <div class="alert alert-success mb-0">
            <i class="bx bx-check-circle me-2"></i>
            Anda sudah menyelesaikan absensi hari ini. Sampai jumpa besok!
          </div>
        @endif

      </div>
    </div>

    {{-- Riwayat minggu ini --}}
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Absensi Minggu Ini</h5></div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr><th>Tanggal</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr>
            </thead>
            <tbody>
              @forelse(\App\Models\Attendance::where('employee_id', $employee->id)->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()])->get() as $att)
              <tr>
                <td>{{ $att->attendance_date->translatedFormat('l, d M') }}</td>
                <td>{{ $att->check_in ?? '-' }}</td>
                <td>{{ $att->check_out ?? '-' }}</td>
                <td>{!! $att->status_badge !!}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let stream, capturedPhoto, gpsData = null, locationId = null;

// Kamera
async function startCamera() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
    document.getElementById('video-preview').srcObject = stream;
  } catch (e) {
    alert('Tidak bisa mengakses kamera: ' + e.message);
  }
}

// GPS
function getLocation() {
  if (!navigator.geolocation) {
    document.getElementById('gps-status').textContent = 'GPS tidak tersedia';
    return;
  }
  navigator.geolocation.getCurrentPosition(async (pos) => {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    gpsData = { latitude: lat, longitude: lng };

    const res = await fetch('{{ route("api.attendance.validate-location") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ latitude: lat, longitude: lng })
    });
    const data = await res.json();

    const badge = document.getElementById('gps-status');
    if (data.valid) {
      badge.className = 'badge bg-label-success gps-badge';
      badge.innerHTML = '<i class="bx bx-map-pin"></i> ' + data.location;
      locationId = data.location_id;
      const btn = document.getElementById('btn-checkin') || document.getElementById('btn-checkout');
      if (btn) btn.disabled = false;
    } else {
      badge.className = 'badge bg-label-danger gps-badge';
      badge.innerHTML = '<i class="bx bx-x-circle"></i> Di luar radius kantor';
    }
  }, (err) => {
    document.getElementById('gps-status').innerHTML = '<i class="bx bx-x-circle"></i> GPS ditolak';
  });
}

// Ambil foto dari video
function capturePhoto() {
  const video = document.getElementById('video-preview');
  const canvas = document.getElementById('canvas-preview');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0);
  return canvas.toDataURL('image/jpeg', 0.8);
}

// Check-in
const btnCheckIn = document.getElementById('btn-checkin');
if (btnCheckIn) {
  btnCheckIn.addEventListener('click', async () => {
    btnCheckIn.disabled = true;
    btnCheckIn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    const photo = capturePhoto();
    const res = await fetch('{{ route("attendances.checkin.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ photo, ...gpsData, location_id: locationId })
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Gagal check-in');
      btnCheckIn.disabled = false;
      btnCheckIn.innerHTML = '<i class="bx bx-camera me-2"></i> Check-in Sekarang';
    }
  });
}

// Check-out
const btnCheckOut = document.getElementById('btn-checkout');
if (btnCheckOut) {
  btnCheckOut.addEventListener('click', async () => {
    btnCheckOut.disabled = true;
    btnCheckOut.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    const photo = capturePhoto();
    const res = await fetch('{{ route("attendances.checkout.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ photo, ...gpsData })
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Gagal check-out');
      btnCheckOut.disabled = false;
      btnCheckOut.innerHTML = '<i class="bx bx-log-out me-2"></i> Check-out Sekarang';
    }
  });
}

// Jam digital
setInterval(() => {
  const el = document.getElementById('current-time');
  if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
}, 1000);

startCamera();
getLocation();
</script>
@endpush
```

---

## BAGIAN 8 — MIDDLEWARE & SERVICE PROVIDER

### 8.1 Update AuthServiceProvider

**File:** `app/Providers/AuthServiceProvider.php`

Tambahkan policy binding jika diperlukan, atau gunakan gate langsung via Spatie.

### 8.2 Tambahkan HasRoles ke User model

**File:** `app/Models/User.php` — tambahkan:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...

    public function employee()
    {
        return $this->hasOne(\App\Models\Employee::class);
    }
}
```

### 8.3 Register middleware

**File:** `bootstrap/app.php` — tambahkan alias middleware:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

---

## BAGIAN 9 — STORAGE & FINALISASI

### 9.1 Buat symbolic link storage

```bash
php artisan storage:link
```

### 9.2 Buat folder penyimpanan foto

```bash
mkdir -p storage/app/public/attendances
mkdir -p storage/app/public/employees
mkdir -p storage/app/public/leaves
```

### 9.3 Set permission folder (Linux/Mac)

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 9.4 Buat queue table dan jalankan

```bash
php artisan queue:table
php artisan migrate
```

### 9.5 Jalankan semua (urutan benar)

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

---

## BAGIAN 10 — AKUN TEST

Setelah seeder berhasil, gunakan akun berikut untuk testing:

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@absensi.local | password |
| HRD | hrd@absensi.local | password |
| Kepala Dept | kepala.it@absensi.local | password |
| Karyawan | karyawan@absensi.local | password |

---

## BAGIAN 11 — CHECKLIST PENGEMBANGAN

Gunakan checklist ini untuk memastikan semua fitur sudah dibuat:

### Fase 1 — Foundation
- [ ] Migrations semua tabel berhasil
- [ ] Seeder roles, permissions, dan data awal berhasil
- [ ] Layout Sneat terpasang, menu demo dihapus
- [ ] Auth (login/logout) berjalan
- [ ] Dashboard menampilkan data berbeda per role

### Fase 2 — Master Data
- [ ] CRUD Departemen (dengan validasi kode unik)
- [ ] CRUD Jabatan (terhubung ke departemen, ada gaji pokok)
- [ ] CRUD Karyawan (dengan foto, terhubung ke user)
- [ ] CRUD Lokasi Kerja (dengan radius)
- [ ] CRUD Komponen Gaji (tunjangan & potongan)

### Fase 3 — Absensi
- [ ] Halaman check-in dengan kamera live (bukan upload)
- [ ] Validasi GPS radius kantor via AJAX
- [ ] Simpan foto + koordinat + timestamp server
- [ ] Hitung keterlambatan otomatis
- [ ] Halaman absensi saya (per karyawan)
- [ ] Halaman semua absensi (HRD)
- [ ] Fitur koreksi absensi oleh HRD

### Fase 4 — Cuti & Lembur
- [ ] Form pengajuan cuti (dengan attachment)
- [ ] Approval/tolak cuti oleh kepala dept / HRD
- [ ] Form pengajuan lembur
- [ ] Approval/tolak lembur + hitung upah otomatis
- [ ] Status badge real-time di list

### Fase 5 — Payroll
- [ ] Halaman generate payroll (pilih periode + karyawan)
- [ ] Engine kalkulasi otomatis (gaji + tunjangan + bonus + lembur - potongan)
- [ ] Detail payroll per karyawan
- [ ] Approval payroll oleh HRD/Super Admin
- [ ] Download slip gaji PDF (template Sneat-style)
- [ ] Halaman slip gaji saya

### Fase 6 — Bonus
- [ ] Form tambah bonus (manual per karyawan)
- [ ] Approval bonus
- [ ] Bonus otomatis masuk payroll bulan terkait

### Fase 7 — Laporan
- [ ] Laporan absensi (filter bulan/dept/karyawan)
- [ ] Export Excel laporan absensi
- [ ] Laporan payroll bulanan
- [ ] Export Excel laporan payroll

---

## CATATAN PENTING

1. **Foto absensi** — Selalu gunakan `getUserMedia` dengan `facingMode: user`, jangan izinkan upload dari galeri. Enforce di sisi server dengan cek apakah data adalah base64 dari stream langsung.

2. **Waktu server** — Selalu gunakan `Carbon::now()` di server untuk timestamp absensi. Jangan percaya waktu dari client/HP.

3. **GPS** — Untuk local testing, browser mungkin butuh HTTPS untuk akses GPS. Gunakan `php artisan serve` dan akses via `127.0.0.1`, bukan `localhost` — biasanya diizinkan tanpa HTTPS.

4. **Permission check** — Selalu gunakan `$this->authorize()` atau `@can` di semua controller dan view. Jangan mengandalkan tampilan menu saja.

5. **Soft delete** — Model karyawan dan departemen menggunakan soft delete. Data tidak benar-benar terhapus dari database.

6. **Payroll idempotent** — Generate payroll untuk periode yang sama pada karyawan yang sama akan mengembalikan yang sudah ada, tidak membuat duplikat.

7. **Queue** — Untuk local testing, queue bisa dijalankan dengan:
   ```bash
   php artisan queue:work
   ```

---

*Dokumen ini dibuat sebagai master prompt untuk membangun sistem absensi lengkap dengan Laravel 11 + Sneat Bootstrap Template.*
*Versi: 1.0 | Target: Local Development*
