<?php
/**
 * Ice code generator implementation action class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Helper\File;
use Ice\Helper\Object;
use Ice\View\Render\Php;

/**
 * Class Action
 *
 * Action code generator
 *
 * @see Ice\Core\Code_Generator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Code_Generator
 *
 * @version 0.0
 * @since 0.0
 */
class Action extends Code_Generator
{
    /**
     * Generate code and other
     *
     * @param $class
     * @param array $data Sended data requered for generate
     * @param bool $force Force if already generate
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function generate($class, $data = null, $force = false)
    {
//        $class = Object::getClass(Action::getClass(), $data);
        $namespace = Object::getNamespace(Action::getClass(), $class);

        $module = Module::getInstance(Object::getModuleAlias($class));

        $path = $module->get(Module::SOURCE_DIR);

//        if ($namespace) {
//            $path .= 'Class/';
//        }

        $filePath = $path . str_replace(['\\', '_'], '/', $class) . '.php';

        $isFileExists = file_exists($filePath);

        if (!$force && $isFileExists) {
            Code_Generator::getLogger()->info(['Action {$0} already created', $class]);
            return;
        }

        $data = [
            'namespace' => rtrim($namespace, '\\'),
            'actionName' => Object::getName($class)
        ];

        $classString = Php::getInstance()->fetch(__CLASS__, $data);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Action {$0} recreated'
            : 'Action {$0} created';

        if ($isFileExists) {
            Code_Generator::getLogger()->info([$message, $class], Logger::SUCCESS);
        }

        Loader::load($class);
    }
}