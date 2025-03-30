<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\JsonDataService;

class ExportShoppingList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-shopping-list {output_file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export shopping list from file';

    /**
     * Execute the console command.
     */
    public function handle(JsonDataService $jsonDataService): void
    {
        $jsonData = $jsonDataService->getJsonData();
        Storage::disk('local')->put($this->argument('output_file_path'), $jsonData);
    }
}
