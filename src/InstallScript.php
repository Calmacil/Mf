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
    }
}