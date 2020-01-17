<?php

namespace RichanFongdasen\I18n\Tests\Supports\Controllers;

use Illuminate\Routing\Controller;

class BarController extends Controller
{
    public function __invoke()
    {
        return 'bar';
    }
}