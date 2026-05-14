<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class PredictionStockService
{
    protected string $scriptPath;

    public function __construct()
    {
        $this->scriptPath = base_path('ml/prediction.py');
    }

    public function prediksi(string $produk, array $history = [], int $leadTime = 7, int $forecastDays = 30): array
    {
        $payload = [
            'produk'        => $produk,
            'lead_time'     => $leadTime,
            'forecast_days' => $forecastDays,
        ];

        if (!empty($history)) {
            $payload['history'] = $history;
        }

        // Tulis payload ke file temp — hindari masalah escaping di Windows shell
        $tmpFile = tempnam(sys_get_temp_dir(), 'pred_') . '.json';
        file_put_contents($tmpFile, json_encode($payload));

        $python = $this->getPythonCommand();
        $cmd    = $python . ' ' . escapeshellarg($this->scriptPath) . ' --file ' . escapeshellarg($tmpFile) . ' 2>NUL';

        Log::debug('[Prediction CMD] ' . $cmd);

        $output = shell_exec($cmd);

        // Hapus file temp
        @unlink($tmpFile);

        Log::debug('[Prediction RAW] ' . substr($output ?? '', 0, 200));

        if (empty(trim($output ?? ''))) {
            throw new Exception("Python tidak menghasilkan output.");
        }

        $start = strpos($output, '{');
        $end   = strrpos($output, '}');

        if ($start === false || $end === false) {
            throw new Exception('Tidak ada JSON ditemukan. Raw: ' . substr($output, 0, 300));
        }

        $jsonLine = substr($output, $start, $end - $start + 1);
        $hasil    = json_decode($jsonLine, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON tidak valid: ' . json_last_error_msg() . ' | ' . $jsonLine);
        }

        if (isset($hasil['error'])) {
            throw new Exception($hasil['error']);
        }

        return $hasil;
    }

    private function getPythonCommand(): string
    {
        $candidates = [
            'C:\Users\TOSHIBA\AppData\Local\Programs\Python\Python311\python.exe',
            'C:\Python311\python.exe',
            'C:\Python310\python.exe',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return '"' . $path . '"';
            }
        }

        return 'python';
    }
}