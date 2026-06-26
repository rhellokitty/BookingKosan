<?php

namespace App\Interface;

interface CategoryRepositoryInterface
{
    public function all();

    public function findBySlug(string $slug);
}
