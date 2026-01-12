<?php

namespace App\Exports\MasterData;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PenaksirExport implements FromView
{
    protected $penaksir;
    protected $strDate;
    protected $endDate;

    public function __construct($penaksir, $strDate, $endDate)
    {
        $this->penaksir = $penaksir;
        $this->strDate = $strDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        if ($this->strDate != null && $this->endDate != null) {
            return view('exports/MasterData/penaksirExport', [
                'penaksir' => $this->penaksir,
                'strDate' => Carbon::parse($this->strDate)->format('d-m-Y'),
                'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            ]);
        } else {
            return view('exports/MasterData/penaksirExport', [
                'penaksir' => $this->penaksir,
                'strDate' => null,
                'endDate' => null,
            ]);
        }
    }
}
