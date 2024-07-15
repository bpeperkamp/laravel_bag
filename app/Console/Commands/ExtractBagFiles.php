<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

class ExtractBagFiles extends Command
{
    const FOLDER = '/bag/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-bag-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'lvbag-extract-nl_' . now()->format('m_d_Y') . '.zip';
        $zip = new ZipArchive;

        $result = $zip->open(Storage::disk('local')->path(self::FOLDER . $filename));

        if ($result) {
            info('Extracting main file:');

            $zip->extractTo(Storage::disk('local')->path(self::FOLDER . '/extracted'));
            $zip->close();

            note('Done with the mainfile!');
        }

        $files = File::allFiles(Storage::disk('local')->path(self::FOLDER . '/extracted'));

        info('Extracting files:');
        note('This will take quite some time. Be patient :-)');
        note('On a fast computer this operation will take around 20 minutes. Again, remember to clean up when running on a schedule.');

        foreach (array_reverse($files) as $file) {
            if ($file->getExtension() == 'zip') {

                // WPL is woonplaats. This is usually 1 file.
                if (Str::startsWith($file->getFilenameWithoutExtension(), '9999WPL')) {
                    $this->extractFile($file, '9999WPL');
                }

                // NUM is nummers. This is usually a large collection of files.
                if (Str::startsWith($file->getFilenameWithoutExtension(), '9999NUM')) {
                    $this->extractFile($file, '9999NUM');
                }

                // PND is pand. This is usually a large collection of files.
                if (Str::startsWith($file->getFilenameWithoutExtension(), '9999PND')) {
                    $this->extractFile($file, '9999PND');
                }

                // VBO is verblijfsobject. This is usually a large collection of files.
                if (Str::startsWith($file->getFilenameWithoutExtension(), '9999VBO')) {
                    $this->extractFile($file, '9999VBO');
                }

                // OPR is openbare ruimte. This is usually a large collection of files.
                if (Str::startsWith($file->getFilenameWithoutExtension(), '9999OPR')) {
                    $this->extractFile($file, '9999OPR');
                }
            }
        }

    }

    public function extractFile($file, $destination)
    {
        $zip = new ZipArchive;

        $result = $zip->open(Storage::disk('local')->path(self::FOLDER . '/extracted/' . $file->getFilename()));

        info('Starting extraction of: ' . $file->getFilename());

        if ($result) {
            $zip->extractTo(Storage::disk('local')->path(self::FOLDER . '/extracted/' . $destination));
            $zip->close();
            note('Finished: ' . $file->getFilename());
        }
    }
}
