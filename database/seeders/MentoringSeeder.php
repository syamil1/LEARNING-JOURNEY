<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentoringSeeder extends Seeder
{
    public function run()
    {
        $ids = [
            77491,
            85830,
            73544,
            84576,
            3765,
        ];

        foreach ($ids as $id) {

            // Cari employee berdasarkan employee_id
            $employee = DB::table('employees')->where('employee_id', $id)->first();

            if (!$employee) {
                dump("Employee not found: " . $id);
                continue;
            }

            // Ambil nama store dari store_id
            $store = DB::table('stores')->where('id', $employee->store_id)->first();

            DB::table('mentorings')->insert([
                'employee_id' => $employee->id,
                'mentor_name' => $this->getDummyMentor(),
                'store' => $store->name ?? 'Unknown Store',
                'notes' => $this->getDummyNotes(),
                'status' => 'pending', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            dump("Inserted mentoring for: " . $employee->name);
        }
    }

    private function getDummyMentor()
    {
        $mentors = [
            'Rizky Ramadhan',
            'Budi Santoso',
            'Siti Nurhaliza',
            'Jonathan Pratama',
            'Dewi Anggraini',
        ];

        return $mentors[array_rand($mentors)];
    }

    private function getDummyNotes()
    {
        $notes = [
            'Employee menunjukkan peningkatan yang baik dalam pelayanan pelanggan.',
            'Butuh pendampingan lebih dalam penguasaan SOP kasir.',
            'Sudah memahami dasar-dasar operasional toko dengan baik.',
            'Perlu meningkatkan kecepatan kerja saat jam sibuk.',
            'Memiliki komunikasi yang baik, cocok menjadi PIC area nanti.',
        ];

        return $notes[array_rand($notes)];
    }
}
