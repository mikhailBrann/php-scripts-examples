<?php
namespace Bin\Classes;
use Bin\Interfaces\WorkDirInterface;

class WorkDirClass implements WorkDirInterface
{
    private $dir = false;
    public function __construct($path)
    {
        if (file_exists($path)) {
            $this->dir = $path;
        } else {
            if(mkdir($path, 0777, true)) {
                $this->dir = $path;
            }
        }
    }

    function scanDir()
    {
        $scanResult = [];

        if($this->dir) {
            $fileList = scandir($this->dir);
            unset($fileList[0],$fileList[1]);
            $scanResult = $fileList;
        }

        return $scanResult;
    }

    function clearDir()
    {
        $scanList = $this->scanDir();

        if (!empty($scanList)) {
            foreach ($scanList as $file) {
                if (is_dir($this->dir.$file)) {
                    $this->clearDir();
                    rmdir($this->dir.$file);
                } else {
                    unlink($this->dir.$file);
                }
            }

        }

        return $this->scanDir();
    }

    function saveFile(string $tmpName, string $fileName)
    {
        $saveResult = move_uploaded_file($tmpName, $this->dir .  $fileName);
        if ($saveResult) {
            return $this->dir .  $fileName;
        } else {
            return false;
        }
    }
}
