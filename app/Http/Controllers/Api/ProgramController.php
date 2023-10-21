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

        //calculate the percentage of the population for each program added for the past 7 days
        $sevenDaysAgo = now()->subDays(7);
        $bsit_last_seven_days = Student::where('program_id', $this->BSIT)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->count();
        $bsis_last_seven_days = Student::where('program_id', $this->BSIS)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->count();
        $bscs_last_seven_days = Student::where('program_id', $this->BSCS)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->count();

        return response()->json([
            'message' => 'Students retrieved',
            'students' => [
                [
                    'name' => 'Information Technology',
                    'currentPopulation' => $bsit,
                    'totalPopulation' => $total_number_of_bsit_students,
                    'percentage' => $percentageOfBsit,
                    'lastSevenDays' => $bsit_last_seven_days,
                ],
                [
                    'name' => 'Information System',
                    'currentPopulation' => $bsis,
                    'totalPopulation' => $total_number_of_bsis_students,
                    'percentage' => $percentageOfBsis,
                    'lastSevenDays' => $bsis_last_seven_days,
                ],
                [
                    'name' => 'Computer Science',
                    'currentPopulation' => $bscs,
                    'totalPopulation' => $total_number_of_bscs_students,
                    'percentage' => $percentageOfBscs,
                    'lastSevenDays' => $bscs_last_seven_days,
                ],
            ],
        ]);
    }
}
