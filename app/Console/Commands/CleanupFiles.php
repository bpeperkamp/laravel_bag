<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;

class CleanupFiles extends Command
{
    const FOLDER = '/bag/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove downloaded files. Possibly saving a lot of diskspace.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $confirmed = confirm('This will delete downloaded BAG files, saving approx. 90Gb. Are you sure?');

        if ($confirmed) {
            Storage::deleteDirectory(self::FOLDER . '/extracted');

            $files = File::allFiles(Storage::disk('local')->path(self::FOLDER));

            foreach ($files as $file) {
                Storage::disk('local')->delete(self::FOLDER . $file->getFilename());
            }
        }
    }
}
