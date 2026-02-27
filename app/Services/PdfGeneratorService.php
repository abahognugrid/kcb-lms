<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfGeneratorService
{
    protected string $view;

    protected array $viewData = [];

    protected string $paperOrientation = 'landscape';

    public function make()
    {
        return Pdf::loadView($this->view, $this->viewData)->setPaper('A4', $this->paperOrientation);
    }

    public function view(string $viewName, array $viewData = [], string $orientation = 'landscape'): self
    {
        $this->view = $viewName;
        $this->viewData = $viewData;
        $this->paperOrientation = $orientation;

        return $this;
    }

    public function stream(?string $filename = null): \Illuminate\Http\Response
    {
        return $this->make()->stream($filename ?? $this->getFileName());
    }

    public function download(?string $filename = null): \Illuminate\Http\Response
    {
        return $this->make()->download($filename ?? $this->getFileName());
    }

    public function streamFromLivewire(?string $filename = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->make();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename ?? $this->getFileName());
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return str($this->view)->afterLast('.')->toString() . now()->toDateString() . '.pdf';
    }
}
