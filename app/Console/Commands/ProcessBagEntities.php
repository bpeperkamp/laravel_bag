<?php

namespace App\Console\Commands;

use App\Models\ResidenceSideAddress;
use App\Services\ValidationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class ProcessBagEntities extends Command
{
    const FOLDER = '/bag/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-bag-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will process the downloaded XML files. It will only process files and fields needed for postalcode resolution. There is a lot more data available, like GIS/poly etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directories = Storage::disk('local')->directories(self::FOLDER . '/extracted');

        info('Processing BAG files. This will take a long time, each file contains around 10.000 entries. Now is the time to get a coffee :-)');

        /** @todo The processN functions could be made generic. */

        foreach ($directories as $directory) {

            // This will process cities (WPL - woonplaats)
            if (Str::of($directory)->endsWith(['9999WPL'])) {
                $files = Storage::disk('local')->allFiles($directory);

                $progress = (object) progress(label: 'Processing city files', steps: count($files));
                $progress->start();
                foreach ($files as $key => $file) {
                    $xmldata = simplexml_load_file(Storage::disk('local')->path($file));
                    $this->processCities($xmldata);
                    $progress->advance();
                }
                $progress->finish();
            }

            // This will process numbers/streets (NUM - nummers)
            if (Str::of($directory)->endsWith(['9999NUM'])) {
                $files = Storage::disk('local')->allFiles($directory);

                $progress = (object) progress(label: 'Processing number files', steps: count($files));
                $progress->start();
                foreach ($files as $key => $file) {
                    $xmldata = simplexml_load_file(Storage::disk('local')->path($file));
                    $this->processNum($xmldata);
                    $progress->advance();
                }
                $progress->finish();
            }

            // This will process public spaces (OPR - openbare ruimte)
            if (Str::of($directory)->endsWith(['9999OPR'])) {
                $files = Storage::disk('local')->allFiles($directory);

                $progress = (object) progress(label: 'Processing public space files', steps: count($files));
                $progress->start();
                foreach ($files as $key => $file) {
                    $xmldata = simplexml_load_file(Storage::disk('local')->path($file));
                    $this->processPublicSpaces($xmldata);
                    $progress->advance();
                }
                $progress->finish();
            }

            // This will process public residences (VBO - verblijfsobject)
            if (Str::of($directory)->endsWith(['9999VBO'])) {
                $files = Storage::disk('local')->allFiles($directory);

                $progress = (object) progress(label: 'Processing residences files', steps: count($files));
                $progress->start();
                foreach ($files as $key => $file) {
                    $xmldata = simplexml_load_file(Storage::disk('local')->path($file));
                    $this->processResidences($xmldata);
                    $progress->advance();
                }
                $progress->finish();
            }

            // This will process premises (PND - pand)
            if (Str::of($directory)->endsWith(['9999PND'])) {
                $files = Storage::disk('local')->allFiles($directory);

                $progress = (object) progress(label: 'Processing premise files', steps: count($files));
                $progress->start();
                foreach ($files as $key => $file) {
                    $xmldata = simplexml_load_file(Storage::disk('local')->path($file));
                    $this->processPremises($xmldata);
                    $progress->advance();
                }
                $progress->finish();
            }

        }

        // Cleanup the files. It will save a lot off space. It will ask for a confirmation.
        $this->call(CleanupFiles::class);

    }

    public function processPremises($xmldata)
    {
        $premises = [];

        foreach ($xmldata->children('sl', true)->standBestand->stand as $child) {
            $item = $child->children('sl-bag-extract', true)->children('Objecten', true)->Pand;

            $identificatie = (int) $item->identificatie->__toString();
            $status = $item->status->__toString();
            $documentdatum = $item->documentdatum ? Carbon::parse($item->documentdatum->__toString()) : null;
            $documentnummer = $item->documentnummer->__toString();
            $geconstateerd = $item->geconstateerd->__toString() == "J";
            $oorspronkelijkBouwjaar = $item->oorspronkelijkBouwjaar ? Carbon::parse($item->oorspronkelijkBouwjaar->__toString()) : null;

            $premises[] = [
                'identificatie' => $identificatie,
                'documentdatum' => $documentdatum,
                'documentnummer' => $documentnummer,
                'geconstateerd' => $geconstateerd,
                'status' => $status,
                'oorspronkelijkBouwjaar' => $oorspronkelijkBouwjaar,
                'created_at' => now()
            ];
        }

        // 10000 entries could be too much to upsert at once
        $collection = collect($premises);

        foreach ($collection->chunk(5000) as $chunk) {
            DB::table('premises')->upsert(
                $chunk->toArray(),
                ['identificatie'],
                [
                    'documentdatum',
                    'documentnummer',
                    'geconstateerd',
                    'status',
                    'oorspronkelijkBouwjaar',
                    'created_at'
                ]
            );
        }
    }

    public function processResidences($xmldata)
    {
        $residences = [];

        foreach ($xmldata->children('sl', true)->standBestand->stand as $child) {
            $item = $child->children('sl-bag-extract', true)->children('Objecten', true)->Verblijfsobject;

            // Nummer Ref (NUM) - singular
            $heeftAlsHoofdadres = $item->heeftAlsHoofdadres->children('Objecten-ref', true);

            // Nummer Ref (NUM) - could be multiple
            $heeftAlsNevenadres = $item->heeftAlsNevenadres->children('Objecten-ref', true);

            // Pand Ref (PND) -singular
            $maaktDeelUitVan = $item->maaktDeelUitVan->children('Objecten-ref', true);

            $identificatie = (int) $item->identificatie->__toString();
            $status = $item->status->__toString();
            $documentdatum = $item->documentdatum->__toString();
            $documentnummer = $item->documentnummer->__toString();
            $geconstateerd = $item->geconstateerd->__toString() == "J";
            $oppervlakte = $item->oppervlakte->__toString();
            $gebruiksdoel = $item->gebruiksdoel->__toString();

            // In some cases, a residence has encompasses more adresses. It does not have more than 1 often, but it happens.
            if ($heeftAlsNevenadres) {
                foreach ($heeftAlsNevenadres as $sideAdress) {
                    ResidenceSideAddress::updateOrCreate(
                        ['residence_identificatie' => $identificatie, 'number_identificatie' => (int) $sideAdress->__toString()],
                        ['residence_identificatie' => $identificatie, 'number_identificatie' => (int) $sideAdress->__toString()]
                    );
                }
            }

            $residences[] = [
                'identificatie' => $identificatie,
                'status' => $status,
                'oppervlakte' => $oppervlakte,
                'gebruiksdoel' => $gebruiksdoel,
                'geconstateerd' => $geconstateerd,
                'documentdatum' => $documentdatum,
                'documentnummer' => $documentnummer,
                'heeftAlsHoofdadres' => $heeftAlsHoofdadres ? (int) $item->heeftAlsHoofdadres->children('Objecten-ref', true)->__toString() : null,
                'maaktDeelUitVan' => $maaktDeelUitVan ? (int) $item->maaktDeelUitVan->children('Objecten-ref', true)->__toString() : null,
                'created_at' => now()
            ];
        }

        // 10000 entries could be too much to upsert at once
        $collection = collect($residences);

        foreach ($collection->chunk(5000) as $chunk) {
            DB::table('residences')->upsert(
                $chunk->toArray(),
                ['identificatie'],
                [
                    'oppervlakte',
                    'gebruiksdoel',
                    'status',
                    'geconstateerd',
                    'documentdatum',
                    'documentnummer',
                    'heeftAlsHoofdadres',
                    'maaktDeelUitVan',
                    'created_at'
                ]
            );
        }
    }

    public function processCities($xmldata)
    {
        $cities = [];

        $validationService = new ValidationService();

        foreach ($xmldata->children('sl', true)->standBestand->stand as $child) {
            $item = $child->children('sl-bag-extract', true)->children('Objecten', true)->Woonplaats;

            foreach ($item->voorkomen->children('Historie', true) as $voorkomen) {
                // Date validity is having some strange/contradicting results. Not sure if i should leave this here.
                if ($validationService->checkBagBeginEndDate($voorkomen->beginGeldigheid->__toString(), $voorkomen->eindGeldigheid->__toString())) {
                    $cities[] = [
                        'naam' => $item->naam->__toString(),
                        'identificatie' => $item->identificatie->__toString(),
                        'created_at' => now()
                    ];
                }
            }
        }

        DB::table('cities')->upsert(
            $cities,
            ['identificatie'],
            [
                'naam',
                'created_at'
            ]
        );
    }

    public function processPublicSpaces($xmldata)
    {
        $spaces = [];

        foreach ($xmldata->children('sl', true)->standBestand->stand as $child) {
            $item = $child->children('sl-bag-extract', true)->children('Objecten', true)->OpenbareRuimte;

            // Woonplaats Ref (WPL)
            $ligtIn = $item->ligtIn->children('Objecten-ref', true);

            $identificatie = (int) $item->identificatie->__toString();
            $naam = $item->naam->__toString();
            $type = $item->type->__toString();
            $status = $item->status->__toString();
            $geconstateerd = $item->geconstateerd->__toString() == "J";
            $documentdatum = $item->documentdatum ? Carbon::parse($item->documentdatum->__toString()) : null;
            $documentnummer = $item->documentnummer->__toString();

            $spaces[] = [
                'identificatie' => $identificatie,
                'naam' => $naam,
                'type' => $type,
                'status' => $status,
                'geconstateerd' => $geconstateerd,
                'documentdatum' => $documentdatum,
                'documentnummer' => $documentnummer,
                'ligtIn' => $ligtIn ? (int) $item->ligtIn->children('Objecten-ref', true)->__toString() : null,
                'created_at' => now()
            ];
        }

        // 10000 entries could be too much to upsert at once
        $collection = collect($spaces);

        foreach ($collection->chunk(5000) as $chunk) {
            DB::table('public_spaces')->upsert(
                $chunk->toArray(),
                ['identificatie'],
                [
                    'naam',
                    'type',
                    'status',
                    'geconstateerd',
                    'documentdatum',
                    'documentnummer',
                    'ligtIn',
                    'created_at'
                ]
            );
        }
    }

    public function processNum($xmldata)
    {
        $numbers = [];

        foreach ($xmldata->children('sl', true)->standBestand->stand as $child) {
            $item = $child->children('sl-bag-extract', true)->children('Objecten', true)->Nummeraanduiding;

            // Openbare Ruimte Ref (OPR)
            $ligtAan = $item->ligtAan->children('Objecten-ref', true);

            // Woonplaats Ref (WPL)
            $ligtIn = $item->ligtIn->children('Objecten-ref', true);

            $identificatie = (int) $item->identificatie->__toString();
            $postcode = $item->postcode->__toString();
            $nummer = (int) $item->huisnummer->__toString();
            $huisletter = $item->huisletter->__toString();

            $numbers[] = [
                'identificatie' => $identificatie,
                'postcode' => $postcode,
                'nummer' => $nummer,
                'huisletter' => $huisletter,
                'ligtIn' => $ligtIn ? (int) $item->ligtIn->children('Objecten-ref', true)->__toString() : null,
                'ligtAan' => $ligtAan ? (int) $item->ligtAan->children('Objecten-ref', true)->__toString() : null,
                'created_at' => now()
            ];
        }

        // 10000 entries could be too much to upsert at once
        $collection = collect($numbers);

        foreach ($collection->chunk(5000) as $chunk) {
            DB::table('numbers')->upsert(
                $chunk->toArray(),
                ['identificatie'],
                [
                    'postcode',
                    'nummer',
                    'huisletter',
                    'ligtIn',
                    'ligtAan',
                    'created_at'
                ]
            );
        }
    }
}
