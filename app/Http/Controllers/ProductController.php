<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyProductsRequest;
use App\Models\Product;
use Exception;
use DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function getInventory()
    {
        // Get all products sorted from oldest to newest
        $products = Product::orderBy('created_at')->get();

        // Return vue file with products
        return Inertia::render('Inventory', [
            'products' => $products,
        ]);
    }

    /**
     * Apply products to the cart.
     *
     * @param  \App\Http\Requests\ApplyProductsRequest  $request
     * @return \Inertia\Response
     */
    public function applyProducts(ApplyProductsRequest $request)
    {
        // Get the validated data from the request
        $validated = $request->validated();
        $applyQuantity = $validated['quantity'];

        DB::beginTransaction();
        try {
            // Get all products with quantity greater than 0
            // Sort products by oldest to newest
            $products = Product::where('quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $appliedProducts = [];
            $totalPrice = 0;

            // Guard clause to prevent applying quantity that is not available
            if ($products->isEmpty())
                throw new Exception('There are no products available.');
            // or applying quantity that is less than or equal to 0
            else if ($applyQuantity <= 0)
                throw new Exception('Quantity must be greater than 0.');
            // or apply quantity that is greater than the total quantity of products
            else if ($applyQuantity > $products->sum('quantity'))
                throw new Exception('Desired quantity is not available.');

            //  Loop through the products
            foreach ($products as $product) {
                if ($applyQuantity >= $product->quantity) {
                    // Add the product details to the applied products array
                    $appliedProducts[] = [
                        'quantity' => $product->quantity,
                        'price' => $product->unit_price,
                        'total_price' => $total = $product->quantity * $product->unit_price,
                    ];

                    // Add the product total to the total price
                    $totalPrice += $total;

                    // Subtract the product quantity to the remaining quantity to apply
                    $applyQuantity -= $product->quantity;

                    // Set the quantity of the product to 0
                    $product->update(['quantity' => 0]);
                    continue;
                }

                // Add the product details to the applied products array
                $appliedProducts[] = [
                    'quantity' => $applyQuantity,
                    'price' => $product->unit_price,
                    'total_price' => $total = $applyQuantity * $product->unit_price,
                ];
                // Add the product total to the total price
                $totalPrice += $total;

                // Subtract the remaining quantity to apply from the product quantity
                $product->update(['quantity' => $product->quantity - $applyQuantity]);

                DB::commit();

                // Return the Vue file of Summary
                return Inertia::render('Applied', [
                    'applied_products' => $appliedProducts,
                    'total_price' => $totalPrice,
                    'isError' => false,
                ]);
            }
        } catch (Exception $e) {
            // Return Vue file of error page
            DB::rollback();
            return Inertia::render('Applied', [
                'applied_products' => $appliedProducts,
                'total_price' => $totalPrice,
                'isError' => true,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }
}
