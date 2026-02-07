<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Printer Name
    |--------------------------------------------------------------------------
    |
    | This is the name of your USB printer as recognized by your system
    | For Windows, this is typically the printer name (e.g., "POS-58")
    | For Linux, this is typically the device path (e.g., "/dev/usb/lp0")
    |
    */
    'default_printer' => env('POS_PRINTER_NAME', 'POS-Printer'),

    /*
    |--------------------------------------------------------------------------
    | Paper Width
    |--------------------------------------------------------------------------
    |
    | This is the width of your thermal paper in characters.
    | For 88mm paper, 48 characters is a good default width.
    |
    */
    'paper_width_chars' => 88,

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | When test mode is enabled, the printer output will be logged instead of
    | being sent to the actual printer. Useful for development.
    |
    */
    'test_mode' => env('PRINTER_TEST_MODE', false),
];
