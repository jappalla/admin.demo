<?php
declare(strict_types=1);

use App\Support\Env;

return [
    'name' => Env::get('APP_NAME', 'Antonio Trapasso CV'),
    'env' => Env::get('APP_ENV', 'local'),
    'debug' => Env::getBool('APP_DEBUG', true),
    'timezone' => Env::get('APP_TIMEZONE', 'Europe/Rome'),
    'base_url' => rtrim((string) Env::get('APP_BASE_URL', ''), '/'),
    'route_mode' => Env::get('APP_ROUTE_MODE', 'path'),
    'seo_title' => Env::get('APP_SEO_TITLE', 'Antonio Trapasso | Curriculum Vitae'),
    'seo_description' => Env::get(
        'APP_SEO_DESCRIPTION',
        'Curriculum vitae personale di Antonio Trapasso: esperienza, competenze e contatti professionali.'
    ),
];
