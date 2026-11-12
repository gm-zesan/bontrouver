<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Schedule::command('listings:check-expiry')->daily()->withoutOverlapping();
Schedule::command('sitemap:generate')->dailyAt('02:00')->withoutOverlapping();

