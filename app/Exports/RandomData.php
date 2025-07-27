<?php

namespace App\Exports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class RandomData implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['ID', 'Name', 'Price', 'Quantity'], // Headers
            [1, 'Product 1', rand(100, 1000), rand(1, 50)],
            [2, 'Product 2', rand(100, 1000), rand(1, 50)],
            [3, 'Product 3', rand(100, 1000), rand(1, 50)],
            [4, 'Product 4', rand(100, 1000), rand(1, 50)],
            [5, 'Product 5', rand(100, 1000), rand(1, 50)],
        ]);
    }
}
