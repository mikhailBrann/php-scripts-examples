<?php
namespace Bin\Classes;
use Bin\Interfaces\CloudInterface;
use Bin\Classes\CloudClass;


class YandexCloudClass extends CloudClass implements CloudInterface
{
    public function getList($path=false)
    {
        if ($path) {
            $request = $this->diskRequest($path=$path);
        } else {
            $request = $this->diskRequest();
        }

        return [
            'response' => $request,
            'path' => $path
        ];
    }

    public function addFolder($path) 
    {
        $request = $this->diskRequest($path, $method="PUT");

        return [
            'response' => $request,
            'path' => $path
        ];
    }


    public function removeElem($path)
    {
        $request = $this->diskRequest($path, $method="DELETE");

        return [
            'response' => $request,
            'path' => $path
        ];
    }

    public function renameElem($path)
    {
        $request = $this->diskRequest($path, $method="POST");

        return [
            'response' => $request,
            'path' => $path
        ];
    }

    public function downloadElem($path)
    {
        $request = $this->diskRequest($path, $method="GET");

        return [
            'response' => $request,
            'path' => $path
        ];
    }

    public function uploadFileLink($path)
    {
        $request = $this->diskRequest($path, $method="GET");

        return [
            'response' => $request,
            'path' => $path
        ];
    }

    public function toSendFile($url, $method, $pathToFile, $fileName)
    {
        $request = $this->sendFile($url, $method, $pathToFile, $fileName);

        return [
            'response' => $request
        ];
    }
}