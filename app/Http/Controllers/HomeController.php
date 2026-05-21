<?php

namespace App\Http\Controllers;

use App\Support\Settings;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'heroTitle' => Settings::get(Settings::HERO_TITLE),
            'heroSubtitle' => Settings::get(Settings::HERO_SUBTITLE),
            'aboutText' => Settings::get(Settings::ABOUT_TEXT),
            'phone' => Settings::get(Settings::PHONE),
            'whatsapp' => Settings::get(Settings::WHATSAPP),
            'telegram' => Settings::get(Settings::TELEGRAM),
            'telegramChannel' => Settings::get(Settings::TELEGRAM_CHANNEL),
            'max' => Settings::get(Settings::MAX),
        ]);
    }
}
