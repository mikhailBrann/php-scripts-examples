<?php
namespace Bin\Interfaces;

interface WorkDirInterface
{
    function scanDir();
    function clearDir();
    function saveFile(string $tmpName, string $fileName);
}