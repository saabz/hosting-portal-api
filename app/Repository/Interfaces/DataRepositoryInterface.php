<?php

namespace App\Repository\Interfaces;

interface DataRepositoryInterface {
    function all($filters): array;
}