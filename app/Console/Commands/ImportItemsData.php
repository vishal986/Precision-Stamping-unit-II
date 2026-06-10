<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Contact;
use Shuchkin\SimpleXLSX;

class ImportItemsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-items {file=Items data.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import items data from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $fullPath = base_path($file);

        if (!file_exists($fullPath)) {
            $this->error("File not found: {$fullPath}");
            return;
        }

        require_once base_path('SimpleXLSX.php');

        $this->info("Parsing Excel file...");

        if ($xlsx = SimpleXLSX::parse($fullPath)) {
            $rows = $xlsx->rows();
            $header = array_shift($rows); // Remove header row
            
            // Expected Header: item_code, name, lfe, client, category, uom, unit_price, cost_price, current_stock, description
            
            $this->output->progressStart(count($rows));

            foreach ($rows as $row) {
                if (empty($row[0])) {
                    $this->output->progressAdvance();
                    continue; // Skip if no item_code
                }

                $itemCode = (string) $row[0];
                $name = (string) $row[1];
                $lfe = (string) $row[2];
                $clientName = (string) $row[3];
                $categoryName = (string) $row[4];
                $uom = (string) $row[5];
                $unitPrice = is_numeric($row[6]) ? (float) $row[6] : 0;
                $costPrice = is_numeric($row[7]) ? (float) $row[7] : 0;
                $currentStock = is_numeric($row[8]) ? (int) $row[8] : 0;
                $description = (string) $row[9];

                // 1. Resolve Category
                $categoryId = null;
                if (!empty($categoryName)) {
                    $category = ItemCategory::firstOrCreate(
                        ['name' => $categoryName],
                        ['type' => 'Goods']
                    );
                    $categoryId = $category->id;
                }

                // 2. Resolve Client
                $clientId = null;
                if (!empty($clientName)) {
                    $client = Contact::firstOrCreate(
                        ['company_name' => $clientName],
                        ['name' => $clientName, 'type' => 'Client']
                    );
                    $clientId = $client->id;
                }

                // 3. Create or Update Item
                Item::updateOrCreate(
                    ['item_code' => $itemCode],
                    [
                        'name' => $name,
                        'lfe' => $lfe,
                        'client_id' => $clientId,
                        'item_category_id' => $categoryId,
                        'uom' => empty($uom) ? 'pcs' : $uom,
                        'unit_price' => $unitPrice,
                        'cost_price' => $costPrice,
                        'current_stock' => $currentStock,
                        'description' => $description,
                        'is_active' => true,
                    ]
                );

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->info("Import completed successfully!");
        } else {
            $this->error(SimpleXLSX::parseError());
        }
    }
}
