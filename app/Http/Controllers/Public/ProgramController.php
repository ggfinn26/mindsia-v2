<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function getActivePaymentPlans(Program $program): JsonResponse
    {
        $plans = $program->activePaymentPlans()->get(['id', 'label', 'type', 'installment_count', 'dp_amount', 'per_installment_amount']);

        return response()->json($plans);
    }
}
