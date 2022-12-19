<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Response;
use Inertia\Testing\AssertableInertia as Assert;


class ApplyProductsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests if the ProductController::applyProducts() method
     *  Checks if the current quantity sum of all products is same with initial quantity sum minus applied quantity
     *
     * @return void
     */
    public function test_products_can_be_applied()
    {

        Product::factory()->count(10)->create();
        $quantity = 5;

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/apply-products', [
                'quantity' => $quantity,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('Applied');

        $this->assertEquals(Product::sum('quantity'), Product::sum('initial_quantity') - $quantity);

    }

    /**
     * Tests if the ProductController::applyProducts() method
     * Checks if a page with the correct dollar amount is returned
     *
     * @return void
     */
    public function test_products_can_be_applied_and_correct_dollar_amount_is_returned()
    {

        $products = [
            [
                'initial_quantity' => 1,
                'quantity' => 1,
                'unit_price' => 10
            ],
            [
                'initial_quantity' => 2,
                'quantity' => 2,
                'unit_price' => 20
            ],
            [
                'initial_quantity' => 2,
                'quantity' => 2,
                'unit_price' => 15
            ],
        ];

        Product::insert($products);
        $quantity = 2;

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/apply-products', [
                'quantity' => $quantity,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Applied')
                    ->has('total_price')
                    ->where('total_price', 30)
            );

    }

    /**
     * Tests if the ProductController::applyProducts() method
     *  Checks if a page with error message is returned when the applied quantity is greater than the sum of initial quantities
     *
     * @return void
     */
    public function test_products_can_not_be_applied_when_applied_quantity_is_greater_than_initial_quantity_sum()
    {

        Product::factory()->count(4)->create();
        $quantity = 100;

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/apply-products', [
                'quantity' => $quantity,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('Error');
        $response->assertSee('Desired quantity is not available.');

    }

    /**
     * Tests if the ProductController::applyProducts() method
     *  Checks if a page with error message is returned when the applied quantity is less than or equal to0
     *
     * @return void
     */
    public function test_products_can_not_be_applied_when_applied_quantity_is_less_than_or_equal_to_0()
    {

        Product::factory()->count(5)->create();
        $quantity = 0;

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/apply-products', [
                'quantity' => $quantity,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('Error');
        $response->assertSee('Quantity must be greater than 0.');

    }

    /**
     * Tests if the ProductController::applyProducts() method
     *  Checks if a page with error message is returned when there are no more products left
     *
     * @return void
     */
    public function test_products_can_not_be_applied_when_there_are_no_more_products_left()
    {

        Product::factory()->count(20)->create([
            'quantity' => 0,
        ]);

        $quantity = 100;

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/apply-products', [
                'quantity' => $quantity,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('Error');
        $response->assertSee('There are no products available.');

    }
}
