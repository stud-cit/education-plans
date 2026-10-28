<?php

namespace Tests\Unit\Helpers;

use App\Helpers\GeneratePlanPdf;
use App\Models\Plan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class GeneratePlanPdfTest extends TestCase
{
    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_it_returns_an_error_code_when_the_plan_does_not_exist(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
        });

        $generator = new GeneratePlanPdf;
        $generator(123);

        $this->assertSame(-1, $generator->getCode());
        $this->assertNull($generator->getPdf());
    }

    public function test_it_rejects_pdf_operations_before_generation(): void
    {
        $generator = new GeneratePlanPdf;

        $this->assertNull($generator->getPdf());
        $this->assertThrows(fn () => $generator->save(), \RuntimeException::class);
        $this->assertThrows(fn () => $generator->output(), \RuntimeException::class);
        $this->assertThrows(fn () => $generator->download(), \RuntimeException::class);
    }

    public function test_output_returns_the_generated_pdf_bytes(): void
    {
        $generator = new GeneratePlanPdf;
        $browser = Mockery::mock(Browsershot::class);
        $browser->shouldReceive('pdf')->once()->andReturn('%PDF-test');
        $this->setGeneratedBrowser($generator, $browser);

        $this->assertSame('%PDF-test', $generator->output());
    }

    public function test_save_passes_the_requested_path_to_browsershot(): void
    {
        $generator = new GeneratePlanPdf;
        $browser = Mockery::mock(Browsershot::class);
        $browser->shouldReceive('save')->once()->with('/tmp/plan.pdf');
        $this->setGeneratedBrowser($generator, $browser);

        $generator->save('/tmp/plan.pdf');
    }

    public function test_download_returns_a_pdf_stream_download(): void
    {
        $generator = new GeneratePlanPdf;
        $browser = Mockery::mock(Browsershot::class);
        $browser->shouldReceive('pdf')->once()->andReturn('%PDF-test');
        $this->setGeneratedBrowser($generator, $browser);

        $response = $generator->download('plan.pdf');

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('plan.pdf', $response->headers->get('Content-Disposition'));
    }

    private function setGeneratedBrowser(GeneratePlanPdf $generator, Browsershot $browser): void
    {
        $property = new \ReflectionProperty(GeneratePlanPdf::class, 'pdf');
        $property->setValue($generator, $browser);
    }
}
