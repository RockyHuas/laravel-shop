<?php

namespace App\Admin\Extensions;

use Illuminate\Support\Collection;

interface ExcelDataInterface
{
    public function exportData(Collection $collection);
}