<?php

namespace App\Exports\MasterData;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StlExport implements FromView
{
    protected $stl;
    protected $strDate;
    protected $endDate;

    public function __construct($stl, $strDate, $endDate)
    {
        $this->stl = $stl;
        $this->strDate = $strDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        if ($this->strDate != null && $this->endDate != null) {
            return view('exports/MasterData/stlExport', [
                'stl' => $this->stl,
                'strDate' => Carbon::parse($this->strDate)->format('d-m-Y'),
                'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            ]);
        } else {
            return view('exports/MasterData/stlExport', [
                'stl' => $this->stl,
                'strDate' => null,
                'endDate' => null,
            ]);
        }
    }
}
