<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomHelpers
{
    public static function getNoSbg(Request $request)
    {
        $year = Carbon::now()->format('y'); // Only get the last two digits of the current year
        $month = Carbon::now()->format('m');
        $posko = substr($request->user()->POSKO, -2); // Example posko value
        // Retrieve the highest existing sequence number for the current month and year from the secondary database
        $latestEntry = DB::connection('second_sqlsrv')->table('tb_transaksi')
            ->where(DB::raw('SUBSTRING(No_sbg, 1, 2)'), '=', $year)
            ->where(DB::raw('SUBSTRING(No_sbg, 3, 2)'), '=', $month)
            ->where(DB::raw('SUBSTRING(No_sbg, 5, 2)'), '=', $posko)
            ->orderBy(DB::raw('CAST(SUBSTRING(No_sbg, 7, 4) AS INT)'), 'desc')
            ->first();

        if ($latestEntry) {
            // Extract the sequence part from the No_sbg value
            $latestSequence = (int)substr($latestEntry->No_sbg, 6, 4);
            $nextSequence = $latestSequence + 1;
        } else {
            $nextSequence = 1;
        }

        // Ensure the sequence number is 4 digits
        $nosbg = $year . $month . $posko . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        return $nosbg;
    }

    public static function getNoSbgPjg(Request $request)
    {
        $abjad = "P";
        $old_nosbg = $request->referensi_nosbg;

        // Check if 'P' is present in the old_nosbg
        $posOfP = strpos($old_nosbg, $abjad);

        if ($posOfP === false) {
            // 'P' is not present, start with base_no_sbg = old_nosbg + "P"
            $base_nosbg = $old_nosbg . $abjad;
            $nextSequence = 1;
        } else {
            // Extract the base_no_sbg (numeric part before 'P')
            $base_nosbg = substr($old_nosbg, 0, $posOfP + 1);

            // Retrieve the highest existing sequence number for the current base_no_sbg from the secondary database
            $latestEntry = DB::connection('second_sqlsrv')->table('tb_transaksi')
                ->where(DB::raw('LEFT(No_sbg, ' . ($posOfP + 1) . ')'), '=', $base_nosbg)
                ->orderBy(DB::raw('CAST(SUBSTRING(No_sbg, ' . ($posOfP + 2) . ', LEN(No_sbg) - ' . ($posOfP + 1) . ') AS INT)'), 'desc')
                ->first();

            if ($latestEntry) {
                // Get the numeric sequence part from the No_sbg, after the character 'P'
                $latestSequence = (int)substr($latestEntry->No_sbg, $posOfP + 1);
                $nextSequence = $latestSequence + 1;
            } else {
                $nextSequence = 1;
            }
        }

        do {
            // Construct the new no_sbg_pjg with the appropriate sequence number
            $no_sbg_pjg = $base_nosbg . $nextSequence;

            // Check if the generated no_sbg_pjg already exists in the table
            $existingEntry = DB::connection('second_sqlsrv')->table('tb_transaksi')
                ->where('No_sbg', '=', $no_sbg_pjg)
                ->first();

            if ($existingEntry) {
                // If it exists, increment the sequence and try again
                $nextSequence++;
            }
        } while ($existingEntry);

        return $no_sbg_pjg;
    }

    public static function getNoBayar(Request $request)
    {
        $abjad = "C";
        $year = Carbon::now()->format('y'); // Only get the last two digits of the current year
        $month = Carbon::now()->format('m');
        $posko = substr($request->user()->POSKO, -2); // Example posko value

        // Retrieve the highest existing sequence number for the current month and year from the secondary database
        $latestEntry = DB::connection('second_sqlsrv')->table('tb_bayar')
            ->where(DB::raw('SUBSTRING(No_bayaran, 2, 2)'), '=', $posko)
            ->where(DB::raw('SUBSTRING(No_bayaran, 4, 2)'), '=', $year)
            ->where(DB::raw('SUBSTRING(No_bayaran, 6, 2)'), '=', $month)
            ->orderBy(DB::raw('CAST(SUBSTRING(No_bayaran, 8, 5) AS INT)'), 'desc')
            ->first();

        if ($latestEntry) {
            // Extract the sequence part from the No_bayaran value
            $latestSequence = (int)substr($latestEntry->No_bayaran, 7, 5);
            $nextSequence = $latestSequence + 1;
        } else {
            $nextSequence = 1;
        }

        // Ensure the sequence number is 5 digits
        $no_bayar = $abjad . $posko . $year . $month . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
        return $no_bayar;
    }

    public static function getNoLelang(Request $request)
    {
        $posko = $request->user()->POSKO;
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('y');

        $latestEntry = DB::connection('second_sqlsrv')->table('TB_AJU_LELANG')
            ->where(DB::raw('SUBSTRING(NOMOR_LELANG, 1, 3)'), '=', $posko)
            ->where(DB::raw('SUBSTRING(NOMOR_LELANG, 4, 2)'), '=', $month)
            ->where(DB::raw('SUBSTRING(NOMOR_LELANG, 6, 2)'), '=', $year)
            ->orderBy(DB::raw('CAST(SUBSTRING(NOMOR_LELANG, 8, 5) AS INT)'), 'desc')
            ->first();

        if ($latestEntry) {
            $latestSequence = (int)substr($latestEntry->NOMOR_LELANG, 7, 5);
            $nextSequence = $latestSequence + 1;
        } else {
            $nextSequence = 1;
        }

        $no_lelang = $posko . $month . $year . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
        return $no_lelang;
    }

    public static function getNoKwitansi()
    {
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('y');

        do {
            $randNum = str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $latestEntry = DB::connection('third_sqlsrv')->table('Mutasi_brg')
                ->where(DB::raw('SUBSTRING(Ket_standar, 8, 3)'), '=', $randNum)
                ->where(DB::raw('SUBSTRING(Ket_standar, 4, 2)'), '=', $month)
                ->where(DB::raw('SUBSTRING(Ket_standar, 6, 2)'), '=', $year)
                ->orderBy(DB::raw('CAST(SUBSTRING(Ket_standar, 11, 5) AS INT)'), 'desc')
                ->first();
        } while ($latestEntry);

        $no_kwitansi = 'KWL' . $month . $year . '0000' . $randNum;
        return $no_kwitansi;
    }
}
