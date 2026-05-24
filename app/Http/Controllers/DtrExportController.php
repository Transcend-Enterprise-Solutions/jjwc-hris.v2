<?php

namespace App\Http\Controllers;

use App\Models\DtrExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DtrExportController extends Controller
{
    public function download($id)
    {
        $export = DtrExport::findOrFail($id);

        // Check if user owns this export
        if ($export->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this export.');
        }

        // Check if export is completed
        if ($export->status !== 'completed' || !$export->file_path) {
            abort(404, 'Export file not found or not ready.');
        }

        // Check if file exists
        if (!Storage::exists($export->file_path)) {
            abort(404, 'Export file not found in storage.');
        }

        // Determine file type
        $extension = pathinfo($export->file_path, PATHINFO_EXTENSION);
        $isZip = $extension === 'zip';
        
        // Generate friendly filename
        if ($isZip) {
            $filename = 'DTR_Reports_' . $export->start_date . '_to_' . $export->end_date . '.zip';
        } else {
            $filename = 'DTR_Report_' . $export->start_date . '_to_' . $export->end_date . '.pdf';
        }

        return Storage::download($export->file_path, $filename);
    }

    public function progress($id)
    {
        $export = DtrExport::findOrFail($id);

        // Check if user owns this export
        if ($export->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this export.');
        }

        return response()->json([
            'progress' => $export->progress,
            'status' => $export->status,
            'message' => $export->status_message,
            'file_path' => $export->file_path,
            'download_url' => $export->status === 'completed' ? route('dtr-export.download', $export->id) : null,
        ]);
    }
}