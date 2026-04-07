<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Slip Gaji — {{ $payroll->period_label }}</title>
<style>
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
  .header { text-align: center; border-bottom: 2px solid #696cff; padding-bottom: 10px; margin-bottom: 20px; }
  .header h2 { margin: 0; color: #696cff; }
  .header p { margin: 4px 0; }
  .info-table, .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
  .info-table td { padding: 4px 6px; }
  .salary-table th, .salary-table td { border: 1px solid #ddd; padding: 6px 10px; }
  .salary-table th { background: #f5f5f9; }
  .total-row { background: #696cff; color: white; font-weight: bold; }
  .footer-note { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <h2>AbsensiApp</h2>
  <p>SLIP GAJI KARYAWAN</p>
  <p><strong>Periode: {{ $payroll->period_label }}</strong></p>
</div>

<table class="info-table">
  <tr><td width="30%"><strong>Nama</strong></td><td>: {{ $payroll->employee->name }}</td>
      <td width="30%"><strong>Kode</strong></td><td>: {{ $payroll->employee->employee_code }}</td></tr>
  <tr><td><strong>Departemen</strong></td><td>: {{ $payroll->employee->department->name }}</td>
      <td><strong>Jabatan</strong></td><td>: {{ $payroll->employee->position->name }}</td></tr>
  <tr><td><strong>Hari Kerja</strong></td><td>: {{ $payroll->working_days }}</td>
      <td><strong>Hadir</strong></td><td>: {{ $payroll->present_days }}</td></tr>
  <tr><td><strong>Terlambat</strong></td><td>: {{ $payroll->late_count }}x</td>
      <td><strong>Status</strong></td><td>: {{ strtoupper($payroll->status) }}</td></tr>
</table>

<table class="salary-table">
  <thead><tr><th>Keterangan</th><th>Tipe</th><th style="text-align:right">Jumlah (Rp)</th></tr></thead>
  <tbody>
    <tr><td>Gaji Pokok</td><td>—</td><td style="text-align:right">{{ number_format($payroll->base_salary, 0, ',', '.') }}</td></tr>
    @foreach($payroll->details as $d)
    <tr>
      <td>{{ $d->label }}</td>
      <td>{{ ['allowance'=>'Tunjangan','deduction'=>'Potongan','bonus'=>'Bonus','overtime'=>'Lembur'][$d->type] ?? $d->type }}</td>
      <td style="text-align:right;color:{{ in_array($d->type,['deduction']) ? 'red' : 'green' }}">
        {{ in_array($d->type,['deduction']) ? '-' : '+' }} {{ number_format($d->amount, 0, ',', '.') }}
      </td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="total-row">
      <td colspan="2" style="text-align:center"><strong>TOTAL GAJI BERSIH</strong></td>
      <td style="text-align:right"><strong>{{ number_format($payroll->net_salary, 0, ',', '.') }}</strong></td>
    </tr>
  </tfoot>
</table>

<div class="footer-note">
  Slip gaji ini digenerate secara otomatis oleh sistem. {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>
