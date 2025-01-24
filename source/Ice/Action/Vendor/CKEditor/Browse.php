<?php

namespace Ice\Action;

use Ice\App;
use Ice\Core\Action;
use Ice\Core\Config;
use Ice\Helper\Directory;
use Ice\Render\Php;

class Vendor_CKEditor_Browse extends Action
{
    protected static function config()
    {
        return [
            'access' => ['roles' => ['ROLE_ICE_ADMIN'], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => [],
            'path' => null
        ];
    }

    public function run(array $input)
    {
        $path = getUploadDir() . Config::getInstance(__CLASS__)->get('path', 'ckeditor/');
        $files = [];
        foreach (new \DirectoryIterator(Directory::get($path)) as $fileInfo) {
            if ($fileInfo->isDot()) continue;

            $files[] = $this->getHostname() . '/ice/ckeditor/' . $fileInfo->getFilename();
        }

        $view = Php::getInstance()->fetch('Ice\Action\Vendor_CKEditor_Browse', ['files' => $files]);
        App::getResponse()->setContent($view);
    }

    private function getHostname()
    {
        $hostname = $this->getHostnameFromSecurityIfAvailable();

        if($hostname){
            return $hostname;
        }

        return $this->getHostnameFromServerEnvironment();
    }

    private function getHostnameFromSecurityIfAvailable()
    {
        $ebsClassname = '\Ebs\Security\Ebs';
        if (class_exists($ebsClassname)) {
            return $ebsClassname::getInstance()->getHost('admin');
        }

        return '';
    }

    private function getHostnameFromServerEnvironment()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
        return $scheme .$_SERVER['HTTP_HOST'];
    }
}