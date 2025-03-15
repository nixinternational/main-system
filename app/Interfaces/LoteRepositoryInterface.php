<?php

namespace App\Interfaces;

use App\Http\Requests\LoteRequest;
use App\Http\Requests\MarcaRequest;

interface LoteRepositoryInterface
{
    public function getIndex();
    public function store(LoteRequest $request);
}
