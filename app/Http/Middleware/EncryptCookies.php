<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'ncore_auth'
    ];

    public function isDisabled($name)
    {
        if (app()->runningUnitTests()) {
            return true;    // Disable cookies encryption/decryption during unit testing
        }

        return parent::isDisabled($name);
    }
}
