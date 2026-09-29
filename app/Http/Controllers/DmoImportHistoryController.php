<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use Illuminate\Http\Request;

class DmoImportHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:dmo,admin');
    }

    public function index(Request $request)
    {
        $typeFilter = $request->input('type');
        $query = ImportLog::with('user');

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $logs = $query->orderBy('imported_at', 'desc')->paginate(15)->withQueryString();

        return view('dmo.import_history', compact('logs', 'typeFilter'));
    }
}
