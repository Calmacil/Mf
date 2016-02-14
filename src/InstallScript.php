<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Mf;


use Composer\Script\Event;

class InstallScript
{
    public static function postInstall(Event $event)
    {
        $dirs = array(
            "cache",
            "config",
            "logs",
            "templates",
            "web",
        );

        foreach ($dirs as $dir) {
            if (!file_exists($dir))
                mkdir($dir, 0755);
        }

        $files = array(
            __DIR__ . "/../resources/settings_dev.json" => "config/settings_dev.json",
            __DIR__ . "/../resources/db.json"           => "config/db.json",
            __DIR__ . "/../resources/routing.json"      => "config/routing.json",
            __DIR__ . "/../resources/index.php"         => "web/index_dev.php"
        );

        foreach ($files as $orig => $dest) {
            if (!file_exists($dest)) {
                copy($orig, $dest);
                chmod($dest, 0644);
            }
        }
    }
}