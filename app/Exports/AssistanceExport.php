<?php

namespace App\Exports;

use App\Models\Assistance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AssistanceExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exportar.excel', ['data' => $this->data]);
    }
}
