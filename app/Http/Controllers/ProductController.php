<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyProductsRequest;
use App\Models\Product;
use Exception;
use DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function getInventory()
    {
        $products = Product::orderBy('created_at')->get();

        return Inertia::render('Inventory', [
            'products' => $products,
        ]);
    }

    public function applyProducts(ApplyProductsRequest $request)
    {
        $validated = $request->validated();
        $applyQuantity = $validated['quantity'];

        DB::beginTransaction();
        try {
            $products = Product::where('quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $appliedProducts = [];
            $totalPrice = 0;

            if($products->isEmpty())
                throw new Exception('There are no products available.');
            else if ($applyQuantity <= 0)
                throw new Exception('Quantity must be greater than 0.');
            else if ($applyQuantity > $products->sum('quantity'))
                throw new Exception('Desired quantity is not available.');

            foreach ($products as $product) {
                if ($applyQuantity >= $product->quantity) {
                    $appliedProducts[] = [
                        'quantity' => $product->quantity,
                        'price' => $product->unit_price,
                        'total_price' => $product->quantity * $product->unit_price,
                    ];
                    $totalPrice += $product->quantity * $product->unit_price;

                    $applyQuantity -= $product->quantity;
                    $product->update(['quantity' => 0]);
                } else {
                    $appliedProducts[] = [
                        'quantity' => $applyQuantity,
                        'price' => $product->unit_price,
                        'total_price' => $applyQuantity * $product->unit_price,
                    ];
                    $totalPrice += $applyQuantity * $product->unit_price;

                    $product->update(['quantity' => $product->quantity - $applyQuantity]);
                    $applyQuantity = 0;
                }

                if ($applyQuantity == 0) {
                    DB::commit();

                    return Inertia::render('Applied', [
                        'applied_products' => $appliedProducts,
                        'total_price' => $totalPrice,
                        'isError' => false,
                    ]);
                }
            }
        } catch(Exception $e){
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
