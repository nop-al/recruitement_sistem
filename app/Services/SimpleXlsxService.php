<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SimpleXlsxService
{
    /**
     * Generate an Excel (.xlsx) file using pure PHP ZipArchive and OpenXML standards.
     *
     * @param  string  $filePath  Target absolute file path
     * @param  array<string>  $headers  Header labels
     * @param  array<array>  $rows  Rows of data
     */
    public static function create(string $filePath, array $headers, array $rows, string $sheetTitle = 'Data'): bool
    {
        $dir = dirname($filePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        // 1. [Content_Types].xml
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>');

        // 2. _rels/.rels
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        // 3. xl/_rels/workbook.xml.rels
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>');

        // 4. xl/workbook.xml
        $cleanTitle = htmlspecialchars($sheetTitle, ENT_XML1, 'UTF-8');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="'.$cleanTitle.'" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        // 5. xl/styles.xml (with styling for bold header with light fill and borders)
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><name val="Calibri"/><sz val="11"/><color rgb="000000"/></font>
    <font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFF"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="4F46E5"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border>
      <left style="thin"><color rgb="CBD5E1"/></left>
      <right style="thin"><color rgb="CBD5E1"/></right>
      <top style="thin"><color rgb="CBD5E1"/></top>
      <bottom style="thin"><color rgb="CBD5E1"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="3">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>
  </cellXfs>
</styleSheet>');

        // 6. xl/worksheets/sheet1.xml
        $colLetter = function (int $index): string {
            $letter = '';
            while ($index >= 0) {
                $letter = chr($index % 26 + 65).$letter;
                $index = intdiv($index, 26) - 1;
            }

            return $letter;
        };

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'."\n";
        $sheetXml .= '  <sheetData>'."\n";

        $rowNum = 1;
        // Header Row (style index 1 = bold white text on indigo fill)
        $sheetXml .= "    <row r=\"{$rowNum}\">\n";
        foreach ($headers as $colIdx => $header) {
            $cellRef = $colLetter($colIdx).$rowNum;
            $cleanVal = htmlspecialchars((string) $header, ENT_XML1, 'UTF-8');
            $sheetXml .= "      <c r=\"{$cellRef}\" t=\"inlineStr\" s=\"1\"><is><t>{$cleanVal}</t></is></c>\n";
        }
        $sheetXml .= "    </row>\n";

        // Data Rows (style index 2 = border)
        foreach ($rows as $row) {
            $rowNum++;
            $sheetXml .= "    <row r=\"{$rowNum}\">\n";
            $colIdx = 0;
            foreach ($row as $val) {
                $cellRef = $colLetter($colIdx).$rowNum;
                if (is_numeric($val) && ! str_starts_with((string) $val, '0')) {
                    $sheetXml .= "      <c r=\"{$cellRef}\" s=\"2\"><v>{$val}</v></c>\n";
                } else {
                    $cleanVal = htmlspecialchars((string) ($val ?? ''), ENT_XML1, 'UTF-8');
                    $sheetXml .= "      <c r=\"{$cellRef}\" t=\"inlineStr\" s=\"2\"><is><t>{$cleanVal}</t></is></c>\n";
                }
                $colIdx++;
            }
            $sheetXml .= "    </row>\n";
        }

        $sheetXml .= "  </sheetData>\n";
        $sheetXml .= '</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return true;
    }

    /**
     * Generate and stream download of an Excel (.xlsx) file.
     */
    public static function download(string $filename, array $headers, array $rows, string $sheetTitle = 'Data Pelamar'): BinaryFileResponse
    {
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir.'/'.uniqid('export_', true).'.xlsx';
        self::create($tempPath, $headers, $rows, $sheetTitle);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Parse an uploaded Excel (.xlsx) file into an array of associative or indexed rows.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function parse(string $filePath): array
    {
        if (! file_exists($filePath)) {
            return [];
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $sharedXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXmlContent) {
            $xml = @simplexml_load_string($sharedXmlContent);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $textParts = [];
                        foreach ($si->r as $run) {
                            $textParts[] = (string) ($run->t ?? '');
                        }
                        $sharedStrings[] = implode('', $textParts);
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read sheet1
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (! $sheetXmlContent) {
            $zip->close();

            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (! $sheetXml || ! isset($sheetXml->sheetData->row)) {
            $zip->close();

            return [];
        }

        $parsedRows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowNum = (int) ($row['r'] ?? count($parsedRows) + 1);
            $cols = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r']; // e.g. "A1", "B2"
                $colLetter = preg_replace('/\d+/', '', $ref);
                $type = (string) ($cell['t'] ?? '');
                $val = (string) ($cell->v ?? '');

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $val = $sharedStrings[(int) $val];
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                }

                $cols[$colLetter] = trim($val);
            }
            $parsedRows[$rowNum] = $cols;
        }

        $zip->close();

        return $parsedRows;
    }
}
