<?php

use App\Console\Commands\ServeCommand;
use Illuminate\Console\Application as ArtisanConsole;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

ArtisanConsole::starting(function ($artisan) {
    $artisan->add(app(ServeCommand::class));
});

