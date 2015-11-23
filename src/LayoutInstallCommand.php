<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maikovisky\LaravelPlus;

use Illuminate\Console\Command;
use Maikovisky\LaravelPlus\PackagerHelper;

/**
 * Description of LayoutInstall
 *
 * @author maiko
 */
class LayoutInstallCommand extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'layout:install {theme=sb-admin2}';
    
     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a layout.';
    /**
     * Packager helper class.
     * @var object
     */
    protected $helper;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PackagerHelper $helper)
    {
        parent::__construct();
        $this->helper = $helper;
    }
    
    public function handle()
    {
         
        $theme = $this->argument('theme');
        
        $publicPath = getcwd() . '/public';
        $resourcePath = getcwd() .'/resources';
        $themesPath = __DIR__ . '/themes/' . $theme;
        
        if(!file_exists($themesPath)) {
            $this->error("Layout '$theme' not exists...");
            return;
        }
        
        // Start the progress bar
        $this->bar = $this->helper->barSetup($this->output->createProgressBar(4));
        $this->bar->start();
        
        $this->helper->rcopy($themesPath.'/public', $publicPath);
        $this->bar->advance();
        
        $this->helper->makeDir($resourcePath ."/views/errors");
        $this->helper->rcopy($themesPath . '/resources/views/errors', 
                $resourcePath ."/views/errors");
        $this->bar->advance();
        
        $this->helper->makeDir($resourcePath ."/views/includes");
        $this->helper->rcopy($themesPath . '/resources/views/includes', 
                $resourcePath . "/views/includes");
        $this->bar->advance();
        
        $this->helper->makeDir($resourcePath ."/views/layouts");
        $this->helper->rcopy($themesPath . '/resources/views/layouts', 
                $resourcePath ."/views/layouts");
        $this->bar->advance();
        
        $this->info("Layout $theme copy...");
    }
    
}
