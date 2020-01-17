<?php

namespace RichanFongdasen\I18n\Tests\Supports\Controllers;

use Illuminate\Routing\Controller;

class FooController extends Controller
{
    public function __invoke()
    {
        return 'foo';
    }
}