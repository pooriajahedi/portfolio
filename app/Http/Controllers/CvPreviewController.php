<?php

namespace App\Http\Controllers;

use App\Services\Cv\CvPdfGenerator;
use Illuminate\Contracts\View\View;

class CvPreviewController extends Controller
{
    public function __invoke(CvPdfGenerator $generator): View
    {
        return view('cv.template', $generator->previewDataForWeb());
    }
}
