<?php

namespace Maikovisky\LaravelPlus;

use ZipArchive;
use RuntimeException;
use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;

/**
 * Helper functions for the Packager commands.
 *
 * @package Packager
 * @author JeroenG
 * @link https://github.com/Jeroen-G/laravel-packager/blob/master/src/PackagerHelper.php
 * 
 **/
class PackagerHelper
{
    /**
     * The filesystem handler.
     * @var object
     */
    protected $files;

    /**
     * Create a new instance.
     * @param Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Open haystack, find and replace needles, save haystack.
     *
     * @param  string $oldFile The haystack
     * @param  mixed  $search  String or array to look for (the needles)
     * @param  mixed  $replace What to replace the needles for?
     * @param  string $newFile Where to save, defaults to $oldFile
     *
     * @return void
     */
    public function replaceAndSave($oldFile, $search, $replace, $newFile = null)
    {
        $newFile = ($newFile == null) ? $oldFile : $newFile;
        $file = $this->files->get($oldFile);
        $replacing = str_replace($search, $replace, $file);
        $this->files->put($newFile, $replacing);
    }

    /**
     * Check if the package already exists.
     *
     * @param  string $path   Path to the package directory
     * @param  string $vendor The vendor
     * @param  string $name   Name of the package
     *
     * @return void          Throws error if package exists, aborts process
     */
    public function checkExistingPackage($path, $vendor, $name)
    {
        if (is_dir($path.$vendor.'/'.$name)) {
            throw new RuntimeException('Package already exists');
        }
    }

    /**
     * Create a directory if it doesn't exist.
     *
     * @param  string $path Path of the directory to make
     *
     * @return void
     */
    public function makeDir($path)
    {
        if (!is_dir($path)) {
            return mkdir($path, 0777, true);
        }
    }

    /**
     * Remove a directory if it exists.
     *
     * @param  string $path Path of the directory to remove.
     *
     * @return void
     */
    public function removeDir($path)
    {
        if ($path == 'packages' or $path == '/') {
            return false;
        }

        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            if (is_dir("$path/$file")) {
                $this->removeDir("$path/$file");
            } else {
                @chmod("$path/$file", 0777);
                @unlink("$path/$file");
            }

        }
        return rmdir($path);
    }

    /**
     * Generate a random temporary filename for the package zipfile.
     *
     * @return string
     */
    public function makeFilename()
    {
        return getcwd().'/package'.md5(time().uniqid()).'.zip';
    }

    /**
     * Download the temporary Zip to the given file.
     *
     * @param  string  $zipFile
     * @param  string  $source
     *
     * @return $this
     */
    public function download($zipFile, $source)
    {
        $client = new Client(['verify' => env('CURL_VERIFY', true)]);
        $response = $client->get($source);
        file_put_contents($zipFile, $response->getBody());
        return $this;
    }

    /**
     * Extract the zip file into the given directory.
     *
     * @param  string  $zipFile
     * @param  string  $directory
     *
     * @return $this
     */
    public function extract($zipFile, $directory)
    {
        $archive = new ZipArchive;
        $archive->open($zipFile);
        $archive->extractTo($directory);
        $archive->close();
        return $this;
    }

    /**
     * Clean-up the Zip file.
     *
     * @param  string  $zipFile
     *
     * @return $this
     */
    public function cleanUp($zipFile)
    {
        @chmod($zipFile, 0777);
        @unlink($zipFile);
        return $this;
    }
    
    
    public function listDir($source)
    {
        $ret = array();
        
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    if(is_dir($source."/".$file)){
                        $ret[] = $file;
                    }
                }
            }
            closedir($dir_handle);
        }
        
        return $ret;
    }
    
    /**
     * Copy recursive 
     * @param type $source
     * @param type $dest
     */
    public function rcopy($source, $dest)
    {
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    if(is_dir($source."/".$file)){
                        if(!is_dir($dest."/".$file)){
                            mkdir($dest."/".$file);
                        }
                        $this->rcopy($source."/".$file, $dest."/".$file);
                    } else {
                        copy($source."/".$file, $dest."/".$file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            copy($source, $dest);
        }
    }
    
}