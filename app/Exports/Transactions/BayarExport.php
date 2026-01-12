<?php

namespace App\Exports\Transactions;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BayarExport implements FromView
{
    protected $bayar;
    protected $strDate;
    protected $endDate;

    public function __construct($bayar, $strDate, $endDate)
    {
        $this->bayar = $bayar;
        $this->strDate = $strDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        if ($this->strDate != null && $this->endDate != null) {
            return view('exports/Transactions/bayarExport', [
                'bayar' => $this->bayar,
                'strDate' => Carbon::parse($this->strDate)->format('d-m-Y'),
                'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            ]);
        } else {
            return view('exports/Transactions/bayarExport', [
                'bayar' => $this->bayar,
                'strDate' => null,
                'endDate' => null,
            ]);
        }
    }
}
