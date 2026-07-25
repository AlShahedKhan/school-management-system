<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class StudentBulkImport implements ToArray
{
    public function array(array $array): void
    {
        // Exists only to satisfy Excel::toArray() type parameter.
        // All reading and processing is handled in ProcessStudentImportJob.
    }
}
