<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentQrCodeController extends Controller
{
    public function show(Request $request, Student $student)
    {
        $filename = "qrcodes/student-{$student->id}.svg";

        if (!Storage::disk('local')->exists($filename)) {
            Storage::disk('local')->makeDirectory('qrcodes');
            $qrImage = QrCode::format('svg')->size(400)->margin(1)->generate($student->qr_code);
            Storage::disk('local')->put($filename, $qrImage);
        }

        $path = Storage::disk('local')->path($filename);

        if ($request->boolean('download')) {
            return response()->download($path, "qr-{$student->nisn}.svg", ['Content-Type' => 'image/svg+xml']);
        }

        return response()->file($path, ['Content-Type' => 'image/svg+xml']);
    }
}
