<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as BaseValidateSignature;

class ValidateSignature extends BaseValidateSignature
{
    /**
     * The names of the query string parameters that should be ignored.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 
    ];
}
