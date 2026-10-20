<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class CrmXlsxReader
{
    public function rows(UploadedFile $file): array
    {
        if (! class_exists(ZipArchive::class)) {
            $this->invalid('XLSX requires PHP ZIP support on this server. Upload UTF-8 CSV instead.');
        }
        $zip = new ZipArchive;
        if ($zip->open($file->getRealPath()) !== true) {
            $this->invalid('The XLSX archive could not be opened.');
        }
        try {
            if ($zip->numFiles > 500) {
                $this->invalid('The workbook contains too many package entries. Use a simple lead-export sheet.');
            }
            $size = 0;
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entry = $zip->statIndex($index);
                $name = $entry['name'] ?? '';
                $size += (int) ($entry['size'] ?? 0);
                if (str_contains($name, '..') || str_starts_with($name, '/') || str_contains($name, '\\') || preg_match('/vbaProject|externalLinks|embeddings/i', $name)
                    || ($entry['size'] ?? 0) > 20000000 || $size > 30000000) {
                    $this->invalid('Use a workbook without macros, external links or embedded files, under 30 MB uncompressed.');
                }
            }
            $workbook = $this->xml($zip, 'xl/workbook.xml');
            $workbookXPath = new DOMXPath($workbook);
            $sheets = $workbookXPath->query('//*[local-name()="sheets"]/*[local-name()="sheet"]');
            if ($sheets->length !== 1) {
                $this->invalid('Upload a workbook with exactly one lead-export worksheet to avoid silently skipping tabs.');
            }
            $sheetId = $sheets->item(0)->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
            $relations = new DOMXPath($this->xml($zip, 'xl/_rels/workbook.xml.rels'));
            $target = null;
            foreach ($relations->query('//*[local-name()="Relationship"]') as $relation) {
                if ($relation->getAttribute('TargetMode') === 'External') {
                    $this->invalid('External workbook relationships are not supported.');
                }
                if ($relation->getAttribute('Id') === $sheetId) {
                    $target = $relation->getAttribute('Target');
                }
            }
            if (! $target || ! preg_match('~^(?:/xl/|xl/)?worksheets/[A-Za-z0-9_-]+\.xml$~D', $target)) {
                $this->invalid('The worksheet path is invalid.');
            }
            $target = str_starts_with($target, '/xl/') ? ltrim($target, '/') : (str_starts_with($target, 'xl/') ? $target : 'xl/'.$target);
            $strings = [];
            if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                $shared = new DOMXPath($this->xml($zip, 'xl/sharedStrings.xml'));
                foreach ($shared->query('//*[local-name()="si"]') as $item) {
                    $text = '';
                    foreach ($shared->query('.//*[local-name()="t"]', $item) as $part) {
                        $text .= $part->textContent;
                    }
                    if (mb_strlen($text) > 4000 || count($strings) >= 120000) {
                        $this->invalid('Shared strings exceed the bounded export limits.');
                    }
                    $strings[] = $text;
                }
            }
            $sheet = new DOMXPath($this->xml($zip, $target));
            $rows = [];
            foreach ($sheet->query('//*[local-name()="sheetData"]/*[local-name()="row"]') as $row) {
                if (count($rows) >= 2001) {
                    $this->invalid('Split exports into at most 2,000 data rows.');
                }
                $values = [];
                foreach ($sheet->query('./*[local-name()="c"]', $row) as $cell) {
                    if (! $cell instanceof DOMElement) {
                        continue;
                    }
                    if ($sheet->query('./*[local-name()="f"]', $cell)->length) {
                        $this->invalid('Formula cells are not imported. Export calculated values only.');
                    }
                    if (! preg_match('/^([A-Z]{1,3})[1-9][0-9]*$/D', $cell->getAttribute('r'), $match)) {
                        $this->invalid('A worksheet cell reference is invalid.');
                    }
                    $column = 0;
                    foreach (str_split($match[1]) as $letter) {
                        $column = $column * 26 + ord($letter) - 64;
                    }
                    if ($column > 60) {
                        $this->invalid('Use at most 60 columns in the lead-export sheet.');
                    }
                    $value = $sheet->query('./*[local-name()="v"]', $cell)->item(0)?->textContent ?? '';
                    $type = $cell->getAttribute('t');
                    if ($type === 's') {
                        if (! ctype_digit($value) || ! array_key_exists((int) $value, $strings)) {
                            $this->invalid('The workbook has an invalid shared-string reference.');
                        }
                        $value = $strings[(int) $value];
                    } elseif ($type === 'inlineStr') {
                        $value = '';
                        foreach ($sheet->query('./*[local-name()="is"]//*[local-name()="t"]', $cell) as $part) {
                            $value .= $part->textContent;
                        }
                    } elseif ($type === 'e') {
                        $this->invalid('Correct spreadsheet error cells before importing.');
                    }
                    if (mb_strlen($value) > 4000) {
                        $this->invalid('An exported field exceeds 4,000 characters.');
                    }
                    if (isset($values[$column - 1])) {
                        $this->invalid('Duplicate worksheet cell coordinates are not supported.');
                    }
                    $values[$column - 1] = $value;
                }
                if ($values !== []) {
                    $last = max(array_keys($values));
                    $rows[] = array_replace(array_fill(0, $last + 1, ''), $values);
                }
            }
            if ($rows === []) {
                $this->invalid('The worksheet contains no rows.');
            }
            $width = count($rows[0]);

            return array_map(function (array $row) use ($width): array {
                if (count($row) > $width) {
                    $this->invalid('A data row extends beyond the header columns.');
                }

                return array_pad($row, $width, '');
            }, $rows);
        } finally {
            $zip->close();
        }
    }

    private function xml(ZipArchive $zip, string $path): DOMDocument
    {
        $value = $zip->getFromName($path);
        if ($value === false || preg_match('/<!DOCTYPE|<!ENTITY/i', $value)) {
            $this->invalid('The XLSX XML package is missing or contains unsupported declarations.');
        }
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $loaded = $document->loadXML($value, LIBXML_NONET | LIBXML_COMPACT);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        if (! $loaded) {
            $this->invalid('The XLSX XML package is malformed.');
        }

        return $document;
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['file' => $message]);
    }
}
