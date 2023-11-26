<?php
namespace Bin\Interfaces;

interface CloudInterface
{
    public function getList($path);
    public function removeElem($path);
    public function renameElem($path);
    public function downloadElem(string $path);
    public function uploadFileLink($path);
}