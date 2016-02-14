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
        echo "Ça marche mon gros roudoudou\n";
    }
}