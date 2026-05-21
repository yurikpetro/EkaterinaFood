<?php

namespace App\Support;

use App\Models\Setting;

class Settings
{
    public const PHONE = 'phone';

    public const WHATSAPP = 'whatsapp';

    public const TELEGRAM = 'telegram';

    public const TELEGRAM_CHANNEL = 'telegram_channel';

    public const MAX = 'max';

    public const PICKUP_ADDRESS = 'pickup_address';

    public const HERO_TITLE = 'hero_title';

    public const HERO_SUBTITLE = 'hero_subtitle';

    public const ABOUT_TEXT = 'about_text';

    public static function defaults(): array
    {
        return [
            self::PHONE => '+7 (900) 000-00-00',
            self::WHATSAPP => '79000000000',
            self::TELEGRAM => '',
            self::TELEGRAM_CHANNEL => '',
            self::MAX => '',
            self::PICKUP_ADDRESS => 'Адрес самовывоза уточняйте по телефону',
            self::HERO_TITLE => 'Домашняя еда от тёти Кати',
            self::HERO_SUBTITLE => 'Вкусные блюда на каждый день, праздники и корпоративы',
            self::ABOUT_TEXT => 'Готовлю с душой: домашние обеды, выпечка к празднику, пицца в школу и корпоративное питание. Закажите онлайн — перезвоню для подтверждения.',
        ];
    }

    public static function get(string $key): string
    {
        return Setting::get($key, self::defaults()[$key] ?? '') ?? '';
    }
}
