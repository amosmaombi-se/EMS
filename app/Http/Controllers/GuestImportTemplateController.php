<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuestImportTemplateController extends Controller
{
    // ─── Column definitions ────────────────────────────────────────────────────
    private const COLUMNS = [
        ['key' => 'first_name *', 'width' => 22, 'hint' => 'Required'],
        ['key' => 'last_name *', 'width' => 22, 'hint' => 'Required'],
        ['key' => 'email', 'width' => 32, 'hint' => 'Optional'],
        ['key' => 'phone *', 'width' => 20, 'hint' => 'Optional'],
        ['key' => 'category', 'width' => 18, 'hint' => 'family/friends/colleagues/business/vip/sponsors/media/other'],
        ['key' => 'guest_type', 'width' => 18, 'hint' => 'primary/plus_one/child/vendor/staff/speaker/performer'],
        ['key' => 'rsvp_status', 'width' => 18, 'hint' => 'pending/attending/not_attending/maybe'],
        ['key' => 'is_vip', 'width' => 12, 'hint' => 'TRUE or FALSE'],
        ['key' => 'plus_one_allowed', 'width' => 20, 'hint' => 'TRUE or FALSE'],
        ['key' => 'plus_ones', 'width' => 14, 'hint' => 'Number 0-10'],
        ['key' => 'language_preference', 'width' => 22, 'hint' => 'en/es/fr/de'],
        ['key' => 'notes', 'width' => 34, 'hint' => 'Optional free text'],
    ];

    private const SAMPLE_ROWS = [
        ['John', 'Smith', 'john.smith@email.com', '25575550101', 'friends', 'primary', 'attending', 'TRUE', 'TRUE', '2', 'en', 'Vegetarian'],
        ['Jane', 'Doe', 'jane.doe@email.com', '25575550102', 'family', 'primary', 'pending', 'FALSE', 'FALSE', '0', 'en', ''],
        ['Carlos', 'Gomez', 'carlos@company.com', '25575550103', 'colleagues', 'primary', 'attending', 'FALSE', 'TRUE', '1', 'es', 'Gluten-free'],
    ];

    // ── Dropdowns applied to data rows (col index => formula string) ───────────
    private const DROPDOWNS = [
        5 => '"family,friends,colleagues,business,vip,sponsors,media,other"',
        6 => '"primary,plus_one,child,vendor,staff,speaker,performer"',
        7 => '"pending,attending,not_attending,maybe"',
        8 => '"TRUE,FALSE"',
        9 => '"TRUE,FALSE"',
        11 => '"en,es,fr,de"',
    ];


    public function __invoke(Request $request, Event $event): StreamedResponse
    {
        $format = strtolower($request->query('format', 'xlsx'));
        return $format === 'csv' ? $this->csvResponse($event) : $this->xlsxResponse($event);
    }

    private function xlsxResponse(Event $event): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Guest Import Template')
            ->setSubject((string) ($event->title ?? 'Event'))
            ->setCreator('GuestList App');

        // ── Sheet 1: data template ─────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Guest Import');

        $this->buildHeaderRow($sheet);
        $this->buildHintRow($sheet);
        $this->buildSampleRows($sheet);
        $this->applyDropdowns($sheet);

        $sheet->freezePane('A3');

        // ── Sheet 2: instructions ──────────────────────────────────────────────
        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle('Instructions');
        $this->buildInstructionsSheet($instructions);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'guest_import_template_' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function buildHeaderRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial', 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'FFFFFF']]],
        ];

        foreach (self::COLUMNS as $i => $col) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $cell = $sheet->getCell("{$colLetter}1");
            $cell->setValue($col['key']);
            $sheet->getStyle("{$colLetter}1")->applyFromArray($headerStyle);
            $sheet->getColumnDimension($colLetter)->setWidth($col['width']);
        }

        $sheet->getRowDimension(1)->setRowHeight(28);
    }

    private function buildHintRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $hintStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'name' => 'Arial', 'size' => 8],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
        ];

        foreach (self::COLUMNS as $i => $col) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getCell("{$colLetter}2")->setValue($col['hint']);
            $sheet->getStyle("{$colLetter}2")->applyFromArray($hintStyle);
        }

        $sheet->getRowDimension(2)->setRowHeight(24);
    }

    private function buildSampleRows(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sampleStyle = [
            'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => '1E1B4B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
        ];

        foreach (self::SAMPLE_ROWS as $rowIdx => $row) {
            $excelRow = $rowIdx + 3; // rows start at 3 (1=header, 2=hints)
            foreach ($row as $colIdx => $value) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->getCell("{$colLetter}{$excelRow}")->setValue($value);
                $sheet->getStyle("{$colLetter}{$excelRow}")->applyFromArray($sampleStyle);
            }
            $sheet->getRowDimension($excelRow)->setRowHeight(18);
        }
    }

    private function applyDropdowns(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        foreach (self::DROPDOWNS as $colIdx => $formula) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            for ($row = 3; $row <= 1002; $row++) {
                $validation = $sheet->getCell("{$colLetter}{$row}")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_INFORMATION)
                    ->setAllowBlank(true)
                    ->setShowDropDown(false)
                    ->setFormula1($formula);
            }
        }
    }

    private function buildInstructionsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->getColumnDimension('A')->setWidth(90);
        $sheet->setShowGridlines(false);

        $rows = [
            ['Guest Import Instructions', true, 'FFFFFF', '4F46E5', 14, 36],
            ['', false, 'FFFFFF', 'FFFFFF', 10, 10],
            ['REQUIRED COLUMNS', true, '1E1B4B', 'EEF2FF', 11, 22],
            ['  first_name  — Guest\'s first name', false, '374151', 'F9FAFB', 10, 18],
            ['  last_name   — Guest\'s last name', false, '374151', 'F9FAFB', 10, 18],
            ['', false, 'FFFFFF', 'FFFFFF', 10, 10],
            ['OPTIONAL COLUMNS', true, '1E1B4B', 'EEF2FF', 11, 22],
            ['  email               — Used for invitations and duplicate detection', false, '374151', 'F9FAFB', 10, 18],
            ['  phone               — Contact number', false, '374151', 'F9FAFB', 10, 18],
            ['  category            — family | friends | colleagues | business | vip | sponsors | media | other', false, '374151', 'F9FAFB', 10, 18],
            ['  guest_type          — primary | plus_one | child | vendor | staff | speaker | performer', false, '374151', 'F9FAFB', 10, 18],
            ['  rsvp_status         — pending | attending | not_attending | maybe  (default: pending)', false, '374151', 'F9FAFB', 10, 18],
            ['  is_vip              — TRUE or FALSE  (default: FALSE)', false, '374151', 'F9FAFB', 10, 18],
            ['  plus_one_allowed    — TRUE or FALSE  (default: FALSE)', false, '374151', 'F9FAFB', 10, 18],
            ['  plus_ones           — Number 0-10  (default: 0)', false, '374151', 'F9FAFB', 10, 18],
            ['  language_preference — en | es | fr | de  (default: en)', false, '374151', 'F9FAFB', 10, 18],
            ['  notes               — Any free-text notes', false, '374151', 'F9FAFB', 10, 18],
            ['', false, 'FFFFFF', 'FFFFFF', 10, 10],
            ['TIPS', true, '1E1B4B', 'FEF9C3', 11, 22],
            ['  • Row 1 is the header — do not delete or rename columns', false, '374151', 'FFFBEB', 10, 18],
            ['  • Row 2 contains hints — you can delete it before importing', false, '374151', 'FFFBEB', 10, 18],
            ['  • Guests with duplicate emails are skipped automatically', false, '374151', 'FFFBEB', 10, 18],
            ['  • Up to 1000 guests per import file', false, '374151', 'FFFBEB', 10, 18],
            ['  • Use the dropdown arrows in cells for valid option values', false, '374151', 'FFFBEB', 10, 18],
        ];

        foreach ($rows as $i => [$text, $bold, $fg, $bg, $size, $height]) {
            $row = $i + 1;
            $cell = $sheet->getCell("A{$row}");
            $cell->setValue($text);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => $bold, 'color' => ['rgb' => $fg], 'name' => 'Arial', 'size' => $size],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            ]);
            $sheet->getRowDimension($row)->setRowHeight($height);
        }
    }
    private function csvResponse(Event $event): StreamedResponse
    {
        $filename = 'guest_import_template_' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            $headers = array_map(
                fn($col) => str_replace(' *', '', $col['key']),
                self::COLUMNS
            );
            fputcsv($handle, $headers);

            fputcsv($handle, array_column(self::COLUMNS, 'hint'));

            foreach (self::SAMPLE_ROWS as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}