<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class ProgramController extends Controller
{
    private $BSIT = 1;
    private $BSIS = 2;
    private $BSCS = 3;

    public function getCountOfPrograms()
    {
        $total_number_of_bsit_students = 728;
        $total_number_of_bsis_students = 526;
        $total_number_of_bscs_students = 408;

        $bsit = Student::where('program_id', $this->BSIT)->count();
        $bsis = Student::where('program_id', $this->BSIS)->count();
        $bscs = Student::where('program_id', $this->BSCS)->count();

        //get the percentage from the total population of BSIT students of
        $percentageOfBsit = ($bsit / $total_number_of_bsit_students) * 100;
        $percentageOfBsis = ($bsis / $total_number_of_bsis_students) * 100;
        $percentageOfBscs = ($bscs / $total_number_of_bscs_students) * 100;

        return response()->json([
            'message' => 'Students retrieved',
            'students' => [
                [
                    'name' => 'Information Technology',
                    'currentPopulation' => $bsit,
                    'totalPopulation' => $total_number_of_bsit_students,
                    'percentage' => $percentageOfBsit,
                ],
                [
                    'name' => 'Information System',
                    'currentPopulation' => $bsis,
                    'totalPopulation' => $total_number_of_bsis_students,
                    'percentage' => $percentageOfBsis,
                ],
                [
                    'name' => 'Computer Science',
                    'currentPopulation' => $bscs,
                    'totalPopulation' => $total_number_of_bscs_students,
                    'percentage' => $percentageOfBscs,
                ],
            ],
        ]);
    }
}
