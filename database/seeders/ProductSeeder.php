<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use App\Models\Product;

class ProductSeeder extends Seeder
{

    /**
     * Subtract the applied quantity from the products
     *
     * @param int $totalQuantityApplied
     * @return void
     */
    private function subtractAppliedQuantity($totalQuantityApplied)
    {
        // Initialize the remaining value to subtract
        $remaining = $totalQuantityApplied;

        DB::beginTransaction();
        try
        {
            // Retrieve the products ordered by the oldest product
            $products = Product::where('quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($products as $product) {
                // Check if the remaining value is greater than or equal to the quantity of the product
                if ($remaining >= $product->quantity) {
                    // Subtract the full quantity of the product from the remaining value
                    $remaining -= $product->quantity;

                    // Update the quantity of the product to 0
                    $product->update(['quantity' => 0]);

                    continue;
                }

                // Subtract the remaining value from the quantity of the product
                $product->update(['quantity' => $product->quantity - $remaining]);
                break;
            }
        }
        catch (Exception $e)
        {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Read the CSV file from public folder
        $csvFile = public_path('initial_transaction.csv');
        $firstRowSkipped = false;

        // Guard clause for non-existing file
        if (($handle = fopen($csvFile, "r")) === FALSE) {
            fclose($handle);
            return;
        }

        // Loop through the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Skip the first row or if the row has less than 2 columns or if row is empty
            if (!$firstRowSkipped || count($row) <= 2 || empty($row[0])) {
                $firstRowSkipped = true;
                continue;
            }

            // Check if the row represents a product purchase
            else if ($row['1'] == 'Purchase') {
                $quantity = $row['2'];
                $price = $row['3'];

                // Insert the product into the database
                DB::table('products')->insert([
                    "created_at" => Carbon::createFromFormat('d/m/Y', $row[0])->toDateTimeString(),
                    "updated_at" => Carbon::createFromFormat('d/m/Y', $row[0])->toDateTimeString(),
                    "quantity" => $quantity,
                    "unit_price" => $price
                ]);

                continue;
            }

            // Else subtract the applied quantity from the products
            $this->subtractAppliedQuantity(-$row[2]);
        }

        // close the file
        fclose($handle);
    }
}
