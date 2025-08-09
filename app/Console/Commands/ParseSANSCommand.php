<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class ParseSANSCommand extends Command
{
    protected $signature = 'sans:parse-pdfs';
    protected $description = 'Extract text from all SANS 10400 PDFs and save as .txt files';

    public function handle()
    {
        $pdfFolder = storage_path('app/sans_refs');
        $outputFolder = storage_path('app/sans_refs_txt');
        $pdftotext = 'C:\Program Files\poppler\bin\poppler-24.08.0\Library\bin\pdftotext.exe'; // ✅ full path

        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0775, true);
        }

        $files = scandir($pdfFolder);
        $this->info("📂 Scanning PDF files in: $pdfFolder");

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                $this->info("📄 Processing: $file");

                try {
                    $pdfPath = $pdfFolder . DIRECTORY_SEPARATOR . $file;
                    $txtFile = $outputFolder . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_FILENAME) . '.txt';

                    // ✅ Use shell command with full path
                    $cmd = "\"$pdftotext\" \"$pdfPath\" \"$txtFile\"";
                    exec($cmd, $output, $code);
                
                    if (!file_exists($txtFile)) {
                        throw new \Exception("Failed to generate text output.");
                    }

                    $this->info("✅ Saved: " . basename($txtFile));
                } catch (\Exception $e) {
                    $this->error("❌ Failed: $file - " . $e->getMessage());
                }
            }
        }

        $this->info('✅ All done!');
    }
}
