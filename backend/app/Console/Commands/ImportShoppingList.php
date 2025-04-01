<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\JsonDataService;

class ImportShoppingList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-shopping-list {input_file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import shopping list from file';

    /**
     * Execute the console command.
     */
    public function handle(JsonDataService $jsonDataService)
    {
        $fileContents = Storage::get($this->argument('input_file_path'));
        if (!Str::isJson($fileContents)) {
          throw new \Exception("File contents are not json");
          return;
        }

        $jsonContents = json_decode($fileContents, true); 
        $errors = $jsonDataService->checkErrorsInJsonData($jsonContents);
        if (!empty($errors)) {
            throw new \Exception("JSON validation errors: ".implode(', ', $errors));
            return;
        }

        $jsonDataService->parseJsonData($jsonContents);
        $jsonDataService->triggerEventChanged();
    }
}
