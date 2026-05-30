<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Budi Santoso',    'phone' => '+6281234567890', 'address' => "Jl. Merdeka No. 12\r\nKec. Sukajadi"],
            ['name' => 'Siti Rahayu',     'phone' => '+6282345678901', 'address' => "Jl. Pahlawan No. 5\r\nKec. Cicendo"],
            ['name' => 'Agus Wijaya',     'phone' => '+6283456789012', 'address' => "Jl. Veteran No. 88\r\nKec. Andir"],
            ['name' => 'Dewi Lestari',    'phone' => '+6284567890123', 'address' => "Jl. Sudirman No. 21\r\nKec. Sumur Bandung"],
            ['name' => 'Rudi Hermawan',   'phone' => '+6285678901234', 'address' => "Jl. Diponegoro No. 34\r\nKec. Regol"],
            ['name' => 'Nia Kurniawati',  'phone' => '+6286789012345', 'address' => "Jl. Ahmad Yani No. 7\r\nKec. Kiaracondong"],
            ['name' => 'Hendra Susanto',  'phone' => '+6287890123456', 'address' => "Jl. Gatot Subroto No. 15\r\nKec. Lengkong"],
            ['name' => 'Maya Putri',      'phone' => '+6288901234567', 'address' => "Jl. Asia Afrika No. 3\r\nKec. Bandung Wetan"],
            ['name' => 'Fajar Nugroho',   'phone' => '+6289012345678', 'address' => "Jl. Soekarno Hatta No. 99\r\nKec. Bojongloa"],
            ['name' => 'Rina Fitriani',   'phone' => '+6281122334455', 'address' => "Jl. Pasteur No. 44\r\nKec. Sukajadi"],
        ];

        $paymentMethods = ['tunai', 'transfer'];

        $notes = [null, null, null, 'Titip ke satpam', 'Hubungi sebelum kirim', null, 'Packing rapi', null];

        $startDate = Carbon::create(2026, 3, 1, 0, 0, 0);
        $endDate   = Carbon::create(2026, 5, 1, 23, 59, 59);

        // Ambil nomor faktur terakhir dari DB, fallback ke 10 (sesuai data existing)
        $lastId = DB::table('transactions')->max('id') ?? 10;
        $facturCounter = $lastId + 1;

        $data = [];

        // Generate ~40 transaksi tersebar dari 1 Mar - 1 Mei 2026
        $totalTransactions = 40;
        $rangeDays = $startDate->diffInDays($endDate); // ~61 hari

        for ($i = 0; $i < $totalTransactions; $i++) {
            $customer      = $customers[array_rand($customers)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $totalItem     = rand(1, 10);
            $pricePerItem  = 150000; // harga satuan produk id=13
            $totalAmount   = $totalItem * $pricePerItem;
            $note          = $notes[array_rand($notes)];

            // Sebar tanggal secara merata
            $daysOffset  = (int) round(($i / $totalTransactions) * $rangeDays);
            $hoursOffset = rand(7, 21);  // jam 07.00 - 21.00
            $minsOffset  = rand(0, 59);
            $secsOffset  = rand(0, 59);

            $createdAt = $startDate->copy()
                ->addDays($daysOffset)
                ->setHour($hoursOffset)
                ->setMinute($minsOffset)
                ->setSecond($secsOffset);

            // updated_at sedikit setelah created_at (0-120 menit)
            $updatedAt = $createdAt->copy()->addMinutes(rand(0, 120));

            $numFactur = 'NF-' . str_pad($facturCounter, 4, '0', STR_PAD_LEFT);
            $facturCounter++;

            $data[] = [
                'num_factur'     => $numFactur,
                'name_customer'  => $customer['name'],
                'no_telephone'   => $customer['phone'],
                'address'        => $customer['address'],
                'recipient'      => $customer['name'],
                'id_products'    => 13,
                'total_item'     => $totalItem,
                'total_amount'   => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => 'completed',
                'notes'          => $note,
                'created_at'     => $createdAt->toDateTimeString(),
                'updated_at'     => $updatedAt->toDateTimeString(),
            ];
        }

        DB::table('transactions')->insert($data);

        $this->command->info("✅ Berhasil insert {$totalTransactions} transaksi (1 Mar - 1 Mei 2026, id_products=13, status=completed).");
    }
}