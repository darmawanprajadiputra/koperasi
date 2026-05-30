# ============================================================
# setup_jadwal_retrain.ps1
# Mendaftarkan retrain.py ke Windows Task Scheduler
# agar berjalan otomatis setiap 7 hari sekali.
#
# Cara pakai:
#   1. Klik kanan file ini → "Run with PowerShell"
#      ATAU jalankan dari PowerShell sebagai Administrator:
#      .\setup_jadwal_retrain.ps1
#
#   2. Script akan menanyakan lokasi Python dan folder proyek.
#   3. Selesai — task terdaftar dan langsung aktif.
# ============================================================

# ── Minta input dari pengguna ────────────────────────────────
Write-Host ""
Write-Host "=== Setup Jadwal Retrain Otomatis (setiap 7 hari) ===" -ForegroundColor Cyan
Write-Host ""

# Path ke python.exe
$defaultPython = (Get-Command python -ErrorAction SilentlyContinue).Source
if (-not $defaultPython) { $defaultPython = "C:\Python311\python.exe" }

$pythonPath = Read-Host "Path ke python.exe [$defaultPython]"
if ([string]::IsNullOrWhiteSpace($pythonPath)) { $pythonPath = $defaultPython }

if (-not (Test-Path $pythonPath)) {
    Write-Host "ERROR: File python.exe tidak ditemukan di: $pythonPath" -ForegroundColor Red
    Write-Host "Periksa kembali path Python Anda." -ForegroundColor Red
    Read-Host "Tekan Enter untuk keluar"
    exit 1
}

# Folder proyek (tempat retrain.py berada)
$defaultDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectDir = Read-Host "Folder proyek (tempat retrain.py) [$defaultDir]"
if ([string]::IsNullOrWhiteSpace($projectDir)) { $projectDir = $defaultDir }

$retrainScript = Join-Path $projectDir "retrain.py"
if (-not (Test-Path $retrainScript)) {
    Write-Host "ERROR: File retrain.py tidak ditemukan di: $retrainScript" -ForegroundColor Red
    Read-Host "Tekan Enter untuk keluar"
    exit 1
}

# Jam mulai retrain (default: tengah malam saat server sepi)
$startTimeInput = Read-Host "Jam mulai retrain setiap harinya (format HH:mm, default 02:00)"
if ([string]::IsNullOrWhiteSpace($startTimeInput)) { $startTimeInput = "02:00" }

# ── Buat folder log ──────────────────────────────────────────
$logDir = Join-Path $projectDir "logs"
if (-not (Test-Path $logDir)) {
    New-Item -ItemType Directory -Path $logDir | Out-Null
    Write-Host "Folder log dibuat: $logDir" -ForegroundColor Gray
}

# ── Nama task ────────────────────────────────────────────────
$taskName = "RetrainLSTM_Penjualan"

# ── Buat Action ──────────────────────────────────────────────
# Menjalankan: python retrain.py >> logs\retrain_YYYY-MM-DD.log 2>&1
# Lewat cmd.exe agar output bisa di-redirect ke file log.
$logFile  = Join-Path $logDir 'retrain_%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%.log'
$argument = "/c `"cd /d `"$projectDir`" && `"$pythonPath`" retrain.py >> `"$logFile`" 2>&1`""

$action = New-ScheduledTaskAction `
    -Execute  "cmd.exe" `
    -Argument $argument `
    -WorkingDirectory $projectDir

# ── Buat Trigger: setiap 7 hari ─────────────────────────────
$startDateTime = [datetime]::Today.AddHours([int]($startTimeInput.Split(":")[0])).AddMinutes([int]($startTimeInput.Split(":")[1]))
$trigger = New-ScheduledTaskTrigger `
    -Once `
    -At $startDateTime `
    -RepetitionInterval (New-TimeSpan -Days 7)

# ── Setting: jalankan meski tidak login, prioritas normal ────
$settings = New-ScheduledTaskSettingsSet `
    -ExecutionTimeLimit    (New-TimeSpan -Hours 3) `
    -StartWhenAvailable `
    -RunOnlyIfNetworkAvailable:$false `
    -WakeToRun:$false

# ── Daftarkan task (hapus dulu kalau sudah ada) ──────────────
$existing = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
if ($existing) {
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    Write-Host "Task lama '$taskName' dihapus dan didaftarkan ulang." -ForegroundColor Yellow
}

try {
    Register-ScheduledTask `
        -TaskName   $taskName `
        -Action     $action `
        -Trigger    $trigger `
        -Settings   $settings `
        -RunLevel   Highest `
        -Description "Retrain model LSTM prediksi penjualan setiap 7 hari" `
        | Out-Null

    Write-Host ""
    Write-Host "=== Berhasil! ===" -ForegroundColor Green
    Write-Host "Task '$taskName' sudah terdaftar." -ForegroundColor Green
    Write-Host ""
    Write-Host "Detail:" -ForegroundColor Cyan
    Write-Host "  Python      : $pythonPath"
    Write-Host "  Script      : $retrainScript"
    Write-Host "  Jadwal      : setiap 7 hari, pukul $startTimeInput"
    Write-Host "  Log output  : $logDir\retrain_YYYY-MM-DD.log"
    Write-Host ""
    Write-Host "Untuk cek atau ubah jadwal, buka:" -ForegroundColor Gray
    Write-Host "  Task Scheduler → Task Scheduler Library → $taskName" -ForegroundColor Gray
    Write-Host ""

} catch {
    Write-Host ""
    Write-Host "ERROR saat mendaftarkan task: $_" -ForegroundColor Red
    Write-Host "Coba jalankan PowerShell sebagai Administrator." -ForegroundColor Yellow
}

Read-Host "Tekan Enter untuk keluar"
