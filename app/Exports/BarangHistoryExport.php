<?php

namespace App\Exports;

use App\Models\BarangHistory;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;

class BarangHistoryExport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function array(): array
    {
        return $this->log;
    }
}
