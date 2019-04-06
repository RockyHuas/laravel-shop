<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Files\ExcelFile;

class DataImport extends ExcelFile
{
    public function getFile()
    {
        // Import a user provided file
        $file = Input::file('upfile');

        $filename = $file->storeAs('upload','product.xlsx');

        $file_path='storage/app/'.$filename;
        return $file_path;
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
       $this->repo->importData($collection);
    }
}
