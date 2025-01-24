<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Config;
use Ice\Exception\Http_Not_Found;

class Vendor_CKEditor_GetImage extends Action
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
        $path = getUploadDir() . Config::getInstance(__CLASS__)->get('path', 'ckeditor') . '/' .$input['image_name'];

        if (!file_exists($path)) {
            throw new Http_Not_Found('Файл не найден');
        }

        ob_start();
        readfile($path);
        $finalImage = ob_get_contents();
        $filesize = ob_get_length();
        ob_end_clean();

        header("Content-Type: image/jpeg");
        header("Content-Length: " . $filesize);
        echo $finalImage;
        exit;
    }
}