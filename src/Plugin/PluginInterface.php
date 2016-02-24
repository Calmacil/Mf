<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Calma\Mf\Plugin;

interface PluginInterface
{

    /**
     * PluginInterface constructor.
     *
     * Inits the plugin
     *
     * @param Calma\Mf\Application $app
     * @param array|\stdClass $options
     */
    public function __construct(&$app, $options);
}