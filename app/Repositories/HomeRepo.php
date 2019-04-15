<?php

namespace App\Repositories;

use App\Traits\AdTrait;
use App\Traits\ArticleCategoryTrait;
use App\Traits\BannerTrait;
use App\Traits\brandTrait;
use App\Traits\ProductTrait;

class HomeRepo
{
    use ArticleCategoryTrait;
    use BannerTrait;
    use AdTrait;
    use brandTrait;
    use ProductTrait;
}