<?php
/**
 * Plugin Name: SLiMS Wa Notification Lite
 * Plugin URI: -
 * Description: Manage SLiMS notification with whacenter
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://t.me/drajathasan
 */
use SLiMS\Plugins;
use SLiMS\Wa\Notification\CirculationHook;
use SLiMS\Wa\Notification\MembershipHook;

define('WA_BASE_DIR', __DIR__ . DS);
define('WA_TEMPLATE', WA_BASE_DIR . 'templates' . DS);

require __DIR__ . '/vendor/autoload.php';

$plugin = Plugins::getInstance();

if (method_exists($plugin, 'registerPages')) {
    // Register all pages into SLiMS module
    $plugin->registerPages();

    // Hook register
    Plugins::use(CirculationHook::class)->for(function() {
        Plugins::hook(Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION, 'afterTransaction');
    });

    Plugins::use(MembershipHook::class)->for(function() {
        Plugins::hook(Plugins::MEMBERSHIP_AFTER_SAVE, 'afterSave');
    });
} else {
    Plugins::hook(Plugins::ADMIN_SESSION_AFTER_START, function() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo '<div class="alert alert-danger">
                <p style="font-size: 12pt">
                    Plugin "<strong>SLiMS Wa Notification Lite</strong>" tidak cocok dengan versi SLiMS anda.<br> 
                    Setidaknya berjalan pada SLiMS versi > <code>9.6.1 atau </code> <code>Develop</code>, 
                    versi SLiMS anda yaitu <code>' . SENAYAN_VERSION_TAG . '</code><br>
                    Segera <strong>non aktifkan</strong> plugin tersebut agar peringatan ini tidak muncul kembali
                </p>
            </div>';
        }
    });
}
