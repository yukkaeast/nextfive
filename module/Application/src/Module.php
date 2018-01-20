<?php
/**
 * @package Application
 * @author Yury Ostapenko
 */

namespace Application;

class Module
{
    const VERSION = '0.1.0';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
