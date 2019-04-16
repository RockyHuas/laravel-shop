<?php

namespace App\Repositories;

use App\Traits\brandTrait;
use App\Traits\ProductCatgoryTrait;
use App\Traits\ProductTrait;

class ProductRepo
{
    use brandTrait;
    use ProductTrait;
    use ProductCatgoryTrait;
}