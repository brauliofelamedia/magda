<?php

namespace App\Http\Controllers;
use App\Traits\APICalls;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use APICalls;

    public function getReportAssessments(Request $request)
    {
        $data = $this->getReportAssessment($request->id,$request->locale);
        $data2 = $this->getReportAssessmentPDF($request->id,$request->locale);
        return response()->json([
            'success' => '¡Éxito!',
            'data' => $data,
            'data2' => $data2
        ], 200); 
    }
}
