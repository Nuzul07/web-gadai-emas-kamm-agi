<?php

namespace App\Exports\MasterData;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UsersExport implements FromView
{
    protected $users;
    protected $strDate;
    protected $endDate;

    public function __construct($users, $strDate, $endDate)
    {
        $this->users = $users;
        $this->strDate = $strDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        if ($this->strDate != null && $this->endDate != null) {
            return view('exports/MasterData/usersExport', [
                'users' => $this->users,
                'strDate' => Carbon::parse($this->strDate)->format('d-m-Y'),
                'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            ]);
        } else {
            return view('exports/MasterData/usersExport', [
                'users' => $this->users,
                'strDate' => null,
                'endDate' => null,
            ]);
        }
    }
}
