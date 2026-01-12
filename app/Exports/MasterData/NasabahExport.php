<?php

namespace App\Exports\MasterData;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NasabahExport implements FromView
{
    protected $nasabah;
    protected $strDate;
    protected $endDate;

    public function __construct($nasabah, $strDate, $endDate) {
        $this->nasabah = $nasabah;
        $this->strDate = $strDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        if ($this->strDate != null && $this->endDate != null) {
            return view('exports/MasterData/nasabahExport', [
                'nasabah' => $this->nasabah,
                'strDate' => Carbon::parse($this->strDate)->format('d-m-Y'),
                'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            ]);
        }
        else {
            return view('exports/MasterData/nasabahExport', [
                'nasabah' => $this->nasabah,
                'strDate' => null,
                'endDate' => null,
            ]);
        }
    }
}
