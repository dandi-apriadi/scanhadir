<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class StudentQrCodeController extends Controller
{
    public function show(Request $request, Student $student)
    {
        if (empty($student->qr_code)) {
            abort(404, 'QR Code belum tersedia untuk siswa ini.');
        }
        $student->loadMissing('user');

        try {
            $asset = $this->buildQrAsset($student, 'qr-siswa-' . ($student->nisn ?? $student->id));

            if ($request->boolean('download')) {
                return response()->streamDownload(
                    fn () => print($asset['content']),
                    $asset['filename'],
                    [
                        'Content-Type' => $asset['content_type'],
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'X-Content-Type-Options' => 'nosniff',
                    ]
                );
            }

            return response($asset['content'], 200, [
                'Content-Type' => $asset['content_type'],
                'Content-Disposition' => 'inline; filename="' . $asset['filename'] . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response('Gagal memproses QR: ' . $e->getMessage(), 500);
        }
    }

    public function downloadByNisn(string $nisn, string $filename)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();
        
        if (empty($student->qr_code)) {
            abort(404, 'QR Code belum tersedia.');
        }

        $student->loadMissing('user');

        try {
            $asset = $this->buildQrAsset($student, $filename);

            return response()->streamDownload(
                fn () => print($asset['content']),
                $asset['filename'],
                [
                    'Content-Type' => $asset['content_type'],
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'X-Content-Type-Options' => 'nosniff',
                ]
            );

        } catch (\Throwable $e) {
            report($e);
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    private function buildQrAsset(Student $student, string $baseName): array
    {
        $canUseGd = function_exists('imagecreatetruecolor')
            && function_exists('imagepng')
            && function_exists('imagecolorallocate');

        $safeBaseName = preg_replace('/[^A-Za-z0-9._-]/', '-', $baseName) ?: 'qr-siswa';
        $safeBaseName = preg_replace('/\.(png|svg)$/i', '', $safeBaseName) ?: 'qr-siswa';

        if ($canUseGd) {
            return [
                'content' => $this->generateBrandedPng(
                    $student->qr_code,
                    $student->user?->name,
                    $student->nisn
                ),
                'content_type' => 'image/png',
                'filename' => $safeBaseName . '.png',
            ];
        }

        // FALLBACK: Try external API for PNG if local GD is missing
        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($student->qr_code);
        $fileContent = @file_get_contents($apiUrl);

        if ($fileContent) {
            return [
                'content' => $fileContent,
                'content_type' => 'image/png',
                'filename' => $safeBaseName . '.png',
            ];
        }

        // LAST RESORT: SVG
        return [
            'content' => $this->generateBrandedSvg(
                $student->qr_code,
                $student->user?->name,
                $student->nisn
            ),
            'content_type' => 'image/svg+xml',
            'filename' => $safeBaseName . '.svg',
        ];
    }

    /**
     * Generate a branded PNG QR Code using GD (no imagick needed).
     * Uses bacon/bacon-qr-code for the QR matrix, GD for rendering.
     */
    private function generateBrandedPng(string $qrValue, ?string $studentName, ?string $nisn): string
    {
        // Extract QR matrix from bacon-qr-code encoder
        $qrCode = \BaconQrCode\Encoder\Encoder::encode($qrValue, \BaconQrCode\Common\ErrorCorrectionLevel::H());
        $matrix  = $qrCode->getMatrix();
        $matrixSize = $matrix->getWidth();

        // --- 2. Render QR matrix with GD ---
        $qrPixelSize = 8;              // each QR module = 8px
        $qrMargin    = 4 * $qrPixelSize; // 4-module quiet zone
        $qrDimension = $matrixSize * $qrPixelSize + $qrMargin * 2;

        // Canvas sizes
        $headerH  = 36;
        $paddingV = 20;
        $labelH   = 60;
        $canvasW  = $qrDimension + 40;
        $canvasH  = $headerH + $paddingV + $qrDimension + $paddingV + $labelH;

        $canvas = imagecreatetruecolor($canvasW, $canvasH);

        // Colors
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $black  = imagecolorallocate($canvas, 0,   0,   0);
        $indigo = imagecolorallocate($canvas, 79,  70,  229);

        // White background
        imagefilledrectangle($canvas, 0, 0, $canvasW, $canvasH, $white);

        // Indigo header bar
        imagefilledrectangle($canvas, 0, 0, $canvasW, $headerH, $indigo);

        // Header text
        $headerText = 'ScanHadir - Kartu QR Presensi';
        $textW      = strlen($headerText) * imagefontwidth(2);
        $textX      = (int)(($canvasW - $textW) / 2);
        imagestring($canvas, 2, max(0, $textX), (int)(($headerH - imagefontheight(2)) / 2), $headerText, $white);

        // Draw QR matrix
        $qrOffsetX = (int)(($canvasW - $qrDimension) / 2);
        $qrOffsetY = $headerH + $paddingV;

        // White QR background
        imagefilledrectangle($canvas, $qrOffsetX, $qrOffsetY, $qrOffsetX + $qrDimension, $qrOffsetY + $qrDimension, $white);

        for ($row = 0; $row < $matrixSize; $row++) {
            for ($col = 0; $col < $matrixSize; $col++) {
                if ($matrix->get($col, $row) === 1) {
                    $x1 = $qrOffsetX + $qrMargin + $col * $qrPixelSize;
                    $y1 = $qrOffsetY + $qrMargin + $row * $qrPixelSize;
                    imagefilledrectangle($canvas, $x1, $y1, $x1 + $qrPixelSize - 1, $y1 + $qrPixelSize - 1, $indigo);
                }
            }
        }

        // Student name label
        $labelY   = $qrOffsetY + $qrDimension + $paddingV - 8;
        $name     = mb_strimwidth($studentName ?? 'Siswa', 0, 28, '...');
        $nameW    = strlen($name) * imagefontwidth(4);
        $nameX    = (int)(($canvasW - $nameW) / 2);
        imagestring($canvas, 4, max(0, $nameX), $labelY, $name, $black);

        // NISN label
        $nisnLine = 'NISN: ' . ($nisn ?? '-');
        $nisnW    = strlen($nisnLine) * imagefontwidth(3);
        $nisnX    = (int)(($canvasW - $nisnW) / 2);
        imagestring($canvas, 3, max(0, $nisnX), $labelY + 22, $nisnLine, $indigo);

        // Footer divider
        imagesetthickness($canvas, 2);
        imageline($canvas, 20, $canvasH - 14, $canvasW - 20, $canvasH - 14, $indigo);

        // Capture as PNG binary
        ob_start();
        imagepng($canvas, null, 6);
        $pngData = (string) ob_get_clean();
        imagedestroy($canvas);

        return $pngData;
    }

    /**
     * Generate a high-quality branded SVG QR Code.
     * Includes header bar, student name, and NISN.
     */
    private function generateBrandedSvg(string $qrValue, ?string $studentName, ?string $nisn): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(360, 0),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        
        $qrSvg = $writer->writeString($qrValue);
        
        // Extract the inner SVG content (without xml tag and svg wrapper)
        // We will wrap it in our own viewport to add margins for text
        preg_match('/<svg[^>]*>(.*)<\/svg>/is', $qrSvg, $matches);
        $innerSvg = $matches[1] ?? '';

        $width = 400;
        $height = 540;
        $indigo = '#4F46E5';
        $black = '#0A0A0A';

        $name = htmlspecialchars($studentName ?? 'Siswa', ENT_QUOTES);
        $nisnLine = 'NISN: ' . htmlspecialchars($nisn ?? '-', ENT_QUOTES);

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<svg width=\"$width\" height=\"$height\" viewBox=\"0 0 $width $height\" xmlns=\"http://www.w3.org/2000/svg\">
    <!-- Background -->
    <rect width=\"$width\" height=\"$height\" fill=\"white\" rx=\"16\" />
    
    <!-- Header -->
    <rect width=\"$width\" height=\"44\" fill=\"$indigo\" rx=\"16\" />
    <rect y=\"20\" width=\"$width\" height=\"24\" fill=\"$indigo\" />
    <text x=\"50%\" y=\"28\" dominant-baseline=\"middle\" text-anchor=\"middle\" fill=\"white\" font-family=\"Arial, sans-serif\" font-size=\"14\" font-weight=\"bold\">ScanHadir - Kartu QR Presensi</text>
    
    <!-- QR Code Section -->
    <g transform=\"translate(20, 60)\">
        $innerSvg
    </g>
    
    <!-- Branding Accent -->
    <rect x=\"20\" y=\"430\" width=\"360\" height=\"2\" fill=\"$indigo\" opacity=\"0.2\" />
    
    <!-- Student Info -->
    <text x=\"50%\" y=\"465\" dominant-baseline=\"middle\" text-anchor=\"middle\" fill=\"$black\" font-family=\"Arial, sans-serif\" font-size=\"18\" font-weight=\"bold\">$name</text>
    <text x=\"50%\" y=\"495\" dominant-baseline=\"middle\" text-anchor=\"middle\" fill=\"$indigo\" font-family=\"Arial, sans-serif\" font-size=\"14\" font-weight=\"600\">$nisnLine</text>
    
    <!-- Footer line -->
    <rect x=\"50\" y=\"515\" width=\"300\" height=\"3\" fill=\"$indigo\" rx=\"1.5\" />
</svg>";
    }
}
