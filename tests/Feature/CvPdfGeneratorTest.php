<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\Cv\CvPdfGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Tests\TestCase;

class CvPdfGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * How many distinct glyph codes the first text string of an uncompressed PDF uses.
     *
     * With joining on, mPDF writes the initial, medial and final shape of a letter as different codes; with it
     * off it writes the one isolated letter every time. A render at normal size cannot tell the two apart, the
     * file can.
     */
    private function distinctGlyphs(string $pdf): int
    {
        preg_match('/\[\((.*?)\)\]\s*TJ|\((.*?)\)\s*Tj/s', $pdf, $match);

        $text = preg_replace_callback(
            '/\\\\(.)/s',
            fn (array $escaped): string => match ($escaped[1]) {
                'r' => "\r",
                'n' => "\n",
                't' => "\t",
                default => $escaped[1],
            },
            $match[1] !== '' ? $match[1] : ($match[2] ?? ''),
        );

        return count(array_unique(str_split($text, 2)));
    }

    public function test_persian_letters_are_joined_in_the_pdf(): void
    {
        $pdf = app(CvPdfGenerator::class)->pdfFromHtml('<p dir="rtl">ببببببب</p>', compress: false);

        // initial, medial and final shape of the letter
        $this->assertSame(3, $this->distinctGlyphs($pdf));
    }

    public function test_the_check_catches_shaping_that_is_switched_off(): void
    {
        // The mistake this guards against: `useOTL` on the constructor, where mPDF never looks, instead of
        // inside the font's own `fontdata` entry.
        // Its own temp folder and font key: mPDF caches the font metrics by name, and a copy made without
        // shaping data would otherwise be picked up by the real generator.
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'tempDir' => sys_get_temp_dir().'/mpdf-unshaped-'.uniqid(),
            'fontDir' => array_merge((new ConfigVariables())->getDefaults()['fontDir'], [resource_path('fonts/vazirmatn')]),
            'fontdata' => (new FontVariables())->getDefaults()['fontdata'] + [
                'vazirmatnunshaped' => ['R' => 'Vazirmatn-Regular.ttf', 'B' => 'Vazirmatn-Bold.ttf'],
            ],
            'default_font' => 'vazirmatnunshaped',
            'directionality' => 'rtl',
            'useOTL' => 0xFF,
        ]);
        $mpdf->SetCompression(false);
        $mpdf->WriteHTML('<p dir="rtl">ببببببب</p>');

        $this->assertSame(1, $this->distinctGlyphs($mpdf->Output('', 'S')));
    }

    public function test_the_bold_font_is_embedded_for_bold_text(): void
    {
        $pdf = app(CvPdfGenerator::class)->pdfFromHtml('<p dir="rtl"><b>سلام</b> دنیا</p>');

        $this->assertStringContainsString('Vazirmatn-Bold', $pdf);
        $this->assertStringContainsString('Vazirmatn-Regular', $pdf);
    }

    public function test_generate_stores_a_numbered_pdf_on_the_public_disk(): void
    {
        Storage::fake('public');
        $generator = app(CvPdfGenerator::class);

        $first = $generator->generate();
        $second = $generator->generate();

        $this->assertSame('resume-files/cv-v001.pdf', $first);
        $this->assertSame('resume-files/cv-v002.pdf', $second);
        $this->assertStringStartsWith('%PDF', Storage::disk('public')->get($first));
    }

    public function test_the_browser_preview_renders_the_same_template(): void
    {
        $html = view('cv.template', app(CvPdfGenerator::class)->previewDataForWeb())->render();

        $this->assertStringContainsString('سوابق حرفه‌ای', $html);
        $this->assertStringContainsString('@font-face', $html);
    }

    public function test_a_project_shows_only_its_first_sentences_on_the_cv(): void
    {
        $sentence = 'این یک جمله‌ی نمونه برای آزمودن کوتاه‌سازی متن پروژه است.';
        Project::query()->create([
            'title' => 'نمونه',
            'description' => implode(' ', array_fill(0, 3, $sentence))."\n\n".implode(' ', array_fill(0, 20, $sentence)),
            'is_active' => true,
        ]);

        $description = app(CvPdfGenerator::class)->previewDataForWeb()['projects'][0]['description'];

        $this->assertLessThanOrEqual(360, mb_strlen($description));
        $this->assertStringEndsWith('.', $description);
        $this->assertStringNotContainsString("\n", $description);
    }

    public function test_a_single_long_sentence_is_kept_whole(): void
    {
        $long = str_repeat('کلمه ', 120).'تمام.';
        Project::query()->create(['title' => 'نمونه', 'description' => $long, 'is_active' => true]);

        $this->assertSame($long, app(CvPdfGenerator::class)->previewDataForWeb()['projects'][0]['description']);
    }
}
