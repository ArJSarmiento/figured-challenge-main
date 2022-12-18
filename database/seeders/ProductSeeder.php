<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private function subtractAppliedQuantity($totalQuantityApplied)
    {
        // Initialize the remaining value to subtract
        $remaining = $totalQuantityApplied;

        // Retrieve the products ordered by the least recently purchased first
        $products = Product::orderBy('created_at', 'asc')->get();

        foreach ($products as $product) {
            // Check if the remaining value is greater than or equal to the quantity of the product
            if ($remaining >= $product->quantity) {
                // Subtract the full quantity of the product from the remaining value
                $remaining -= $product->quantity;

                // Update the quantity of the product to 0
                $product->update(['quantity' => 0]);
            } else {
                // Subtract the remaining value from the quantity of the product
                $product->update(['quantity' => $product->quantity - $remaining]);

                // Set the remaining value to 0
                $remaining = 0;
            }

            // Break the loop if the remaining value is 0
            if ($remaining == 0) {
                break;
            }
        }
    }

    public function run()
    {
        $csvFile = public_path('initial_transaction.csv');
        $firstRowSkipped = false;

        if (($handle = fopen($csvFile, "r")) === FALSE) {
            fclose($handle);
            return;
        }
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (!$firstRowSkipped || count($row) <= 2 || empty($row[0])) {
                $firstRowSkipped = true;
                continue;
            }

            if ($row['1'] == 'Purchase') {
                // data represents a product purchase
                $quantity = $row['2'];
                $price = $row['3'];

                // Reset the total applied quantity to 0
                DB::table('products')->insert([
                    "created_at" => Carbon::createFromFormat('d/m/Y', $row[0])->toDateTimeString(),
                    "updated_at" => Carbon::createFromFormat('d/m/Y', $row[0])->toDateTimeString(),
                    "quantity" => $quantity,
                    "unit_price" => $price
                ]);

                continue;
            }

            $this->subtractAppliedQuantity(-$row[2]);
        }
        fclose($handle);
    }
}
