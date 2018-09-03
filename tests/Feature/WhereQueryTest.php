<?php

namespace Eav\TestCase\Feature;

class WhereQueryTest extends TestCase
{
    /** @test */
    public function it_can_add_where_statement()
    {
        $eloquent = $this->product();

        $product = Cars::whereAttribute('sku', 'PDO1HJK92')->get();

        $noProduct = Cars::whereAttribute('sku', 'SSSSSS')->get();

        $this->assertTrue($noProduct->isEmpty());

        $this->assertEquals($eloquent->getKey(), $product->first()->getKey());
    }

    /** @test */
    public function it_can_add_where_or_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereAttribute('sku', 'SSSSSS')
                    ->orWhereAttribute('sku', 'PDO1HJK92')->get();

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);

        $product = Cars::whereAttribute('sku', 'SSSSSS')
                    ->orWhereAttribute('sku', 'like', 'PDO%')->get();

        $this->assertEquals($product->count(), 2);
    }

    /** @test */
    public function it_can_add_where_between_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereBetweenAttribute('age', [18, 100])->get(['sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_or_where_between_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereBetweenAttribute('age', [18, 100])
                    ->orWhereBetweenAttribute('age', [10, 17])
                    ->get();

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 2);
    }


    /** @test */
    public function it_can_add_where_not_between_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereNotBetweenAttribute('age', [18, 100])->get(['age']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->age, 14);
    }

    /** @test */
    public function it_can_add_or_where_not_between_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereNotBetweenAttribute('age', [18, 100])
                    ->orwhereNotBetweenAttribute('age', [10, 13])
                    ->get();

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 2);
    }

    /** @test */
    public function it_can_add_where_in_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereInAttribute('age', [10, 11, 14])
                    ->get();

        $product2 = Cars::whereInAttribute('age', [10, 11, 15])
                    ->get();

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);

        $this->assertTrue($product2->isEmpty());
    }

    /** @test */
    public function it_can_add_or_where_in_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereInAttribute('age', [10, 11, 15])
                    ->orWhereInAttribute('age', [14, 18])
                    ->get();
                       
        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
    }

    /** @test */
    public function it_can_add_where_not_in_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereNotInAttribute('age', [10, 11, 14])
                    ->get();

        $product2 = Cars::whereNotInAttribute('age', [10, 11, 15])
                    ->get();

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);

        $this->assertTrue($product2->isNotEmpty());
        $this->assertEquals($product2->count(), 2);
    }

    /** @test */
    public function it_can_add_or_where_not_in_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereNotInAttribute('sku', ['PDOBEEAM112', 'RAMDOM'])
                    ->orWhereNotInAttribute('age', [14, 18])
                    ->get(['sku']);
                       
        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_where_null_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereNullAttribute('description')
                    ->get(['*', 'description', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_or_where_null_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereAttribute('sku', 'UNKNOWN')
                    ->orWhereNullAttribute('description')
                    ->get(['*', 'description', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_where_not_null_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereNotNullAttribute('description')
                    ->get(['*', 'description', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDOBEEAM112');
    }

    /** @test */
    public function it_can_add_or_where_not_null_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereAttribute('sku', 'UNKNOWN')
                    ->orWhereNotNullAttribute('description')
                    ->get(['*', 'description', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDOBEEAM112');
    }

    /** @test */
    public function it_can_add_where_date_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereDateAttribute('purchased_at', '2018-09-02')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_or_where_date_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereDateAttribute('purchased_at', '2018-09-01')
                    ->orWhereDateAttribute('purchased_at', '=', '2018-09-02')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_where_time_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereTimeAttribute('purchased_at', '=', '15:02:01')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_or_where_time_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereTimeAttribute('purchased_at', '=', '15:04:01')
                    ->orwhereTimeAttribute('purchased_at', '=', '15:03:01')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDOBEEAM112');
    }

    /** @test */
    public function it_can_add_where_day_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereDayAttribute('purchased_at', '02')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_where_month_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereMonthAttribute('purchased_at', '09')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 1);
        $this->assertEquals($product->first()->sku, 'PDO1HJK92');
    }

    /** @test */
    public function it_can_add_where_year_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::whereYearAttribute('purchased_at', '2018')
                    ->get(['*', 'sku']);

        $this->assertTrue($product->isNotEmpty());
        $this->assertEquals($product->count(), 2);
    }

    /** @test */
    public function it_can_add_order_by_statement()
    {
        $eloquent = $this->product();
        $eloquent2 = $this->product_2();

        $product = Cars::orderByAttribute('purchased_at', 'asc')
                    ->get(['*', 'sku']);

        $this->assertEquals($product->first()->sku, 'PDOBEEAM112');
    }

    private function product()
    {
        return Cars::create([
            'name' => 'Flamethrower',
            'sku'  => 'PDO1HJK92',
            'age' => rand(50, 100),
            'search' => 1,
            'purchased_at' => new \DateTime('2018-09-02T15:02:01.012345Z')
        ]);
    }

    private function product_2()
    {
        return Cars::create([
            'name' => 'Space Beem',
            'sku'  => 'PDOBEEAM112',
            'description' => 'Definitely Not a Flamethrower',
            'age' => 14,
            'search' => 0,
            'purchased_at' => new \DateTime('2018-08-21T15:03:01.012345Z')
        ]);
    }
}
