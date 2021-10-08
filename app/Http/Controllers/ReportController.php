<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{


    public $user;
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    public function index()
    {
        return $this->user->reports()->get();
    }
    public function store(Request $request)
    {

        return $this->user->reports()->create($request->all());
    }

    public function show(Report $report)
    {
        return $report;
    }
    public function destroy(Report $report)
    {
        $report->delete();
        return response()->json('deleted');
    }
}
