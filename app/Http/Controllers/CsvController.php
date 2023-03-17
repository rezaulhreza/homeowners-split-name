<?php

namespace App\Http\Controllers;

use App\Actions\CsvParser;
use App\Http\Requests\ProcessCsvRequest;
use Illuminate\Http\JsonResponse;

class CsvController extends Controller
{
    public function processCsv(ProcessCsvRequest $request, CsvParser $csvParser): JsonResponse
    {
        $file = $request->file('csv_file');
        $people = $csvParser->parseCsv($file);

        if (! empty($people)) {
            $csvParser->process($people);
        }

        return response()->json(['data' => $people]);
    }
}
