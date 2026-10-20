<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class CrmXlsxReaderTest extends TestCase
{
    use RefreshDatabase;

    private array $fixturePaths = [];

    protected function tearDown(): void
    {
        foreach ($this->fixturePaths as $path) {
            if (is_file($path) && str_starts_with(basename($path), 'geic-xlsx-test-')) {
                unlink($path);
            }
        }
        parent::tearDown();
    }

    public function test_single_sheet_xlsx_is_previewed_with_shared_strings_and_sparse_cells_preserved(): void
    {
        config(['crm.enabled' => true]);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $sheet = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="s"><v>2</v></c><c r="D1" t="inlineStr"><is><t>city</t></is></c></row><row r="2"><c r="A2" t="inlineStr"><is><t>source-001</t></is></c><c r="B2" t="inlineStr"><is><t>Example student</t></is></c><c r="C2" t="str"><v>9876543210</v></c></row></sheetData></worksheet>';
        $file = $this->fixture($sheet);
        $id = $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris XLSX test', 'file' => $file])->assertOk()->json('data.id');
        $this->getJson('/admin/crm/imports/'.$id)->assertOk()->assertJsonPath('rows.0.fields.source_id', 'source-001')->assertJsonPath('rows.0.fields.full_name', 'Example student')->assertJsonPath('rows.0.fields.city', '')->assertJsonPath('rows.0.errors', []);
    }

    public function test_formulas_and_doctype_are_rejected_before_import(): void
    {
        config(['crm.enabled' => true]);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        foreach ([
            '<worksheet><sheetData><row><c r="A1"><f>HYPERLINK("https://invalid.example")</f><v>1</v></c></row></sheetData></worksheet>',
            '<!DOCTYPE worksheet [<!ENTITY test SYSTEM "file:///etc/passwd">]><worksheet><sheetData/></worksheet>',
        ] as $sheet) {
            $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris XLSX test', 'file' => $this->fixture($sheet)])->assertUnprocessable()->assertJsonValidationErrors('file');
        }
        $this->assertDatabaseCount('crm_imports', 0);
    }

    public function test_multiple_sheets_are_not_silently_ignored(): void
    {
        config(['crm.enabled' => true]);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris XLSX test', 'file' => $this->fixture('<worksheet/>', true)])
            ->assertUnprocessable()->assertJsonValidationErrors('file');
    }

    private function fixture(string $sheet, bool $multiple = false): UploadedFile
    {
        if (! class_exists(ZipArchive::class)) {
            $this->markTestSkipped('The local PHP ZIP extension is unavailable.');
        }
        $path = tempnam(sys_get_temp_dir(), 'geic-xlsx-test-');
        $this->fixturePaths[] = $path;
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('[Content_Types].xml', '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/></Types>');
        $zip->addFromString('xl/workbook.xml', '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Leads" sheetId="1" r:id="rId1"/>'.($multiple ? '<sheet name="Other" sheetId="2" r:id="rId2"/>' : '').'</sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Target="worksheets/sheet1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/></Relationships>');
        $zip->addFromString('xl/sharedStrings.xml', '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><si><t>source_id</t></si><si><t>full_name</t></si><si><t>phone</t></si></sst>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
        $zip->close();

        return new UploadedFile($path, 'export.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}
