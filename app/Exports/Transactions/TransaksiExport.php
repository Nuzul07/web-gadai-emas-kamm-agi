<?php

namespace App\Exports\Transactions;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransaksiExport implements FromView
{
    protected $trans;
    protected $strDate;
    protected $endDate;

    public function __construct($trans, $strDate, $endDate)
    {
        $this->trans = $trans;
        $this->strDate = $strDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        if ($this->strDate != null && $this->endDate != null) {
            return view('exports/Transactions/transExport', [
                'trans' => $this->trans,
                'strDate' => Carbon::parse($this->strDate)->format('d-m-Y'),
                'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            ]);
        } else {
            return view('exports/Transactions/transExport', [
                'trans' => $this->trans,
                'strDate' => null,
                'endDate' => null,
            ]);
        }
    }
}
