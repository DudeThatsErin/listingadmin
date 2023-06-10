<?php
declare(strict_types=1);
/**
 * @project          Listing Admin
 * @copyright        2020
 * @license          GPL Version 3; BSD Modified
 * @author           Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <autoloader.php>
 * @version          Robotess Fork
 */

namespace Robotess;

use function file_exists;

spl_autoload_register([new autoloader(), 'autoload']);

/**
 * Class autoloader
 *
 * @package Robotess
 */
final class autoloader
{
    private string $path;

    public function __construct()
    {
        $this->path = __DIR__ . DIRECTORY_SEPARATOR;
    }

    public function autoload(string $class): void
    {
        if (strpos($class, 'Robotess\\') !== 0) {
            return;
        }

        $filename = $this->path . DIRECTORY_SEPARATOR . str_replace('Robotess\\', '', $class) . '.php';

        if (file_exists($filename)) {
            include $filename;
        }
    }
}
