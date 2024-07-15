<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\confirm;

class GetBagDump extends Command
{
    const FOLDER = '/bag/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-bag-dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will download the zip file containing BAG data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'lvbag-extract-nl_' . now()->format('m_d_Y') . '.zip';

        if (Storage::disk('local')->exists(self::FOLDER . $filename)) {
            $confirmed = confirm('The BAG file for today exists already. Re-download?');
            if (!$confirmed) {
                return;
            }
        }

        info('Downloading BAG file...');
        note('On a fast internet connection this will take around 3-4 mins.');
        note('If you will run this command on a cron schedule, remember to clean up the downloads. Your disk will fill up quickly.');

        $url = 'https://service.pdok.nl/lv/bag/atom/downloads/lvbag-extract-nl.zip';

        $response = Http::timeout(3000)
                        ->withOptions(['stream' => true])
                        ->get($url);

        Storage::disk('local')->put(self::FOLDER . $filename, (string)$response->getBody());
    }
}
