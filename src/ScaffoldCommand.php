<?php

namespace Maikovisky\LaravelPlus;

use Illuminate\Console\Command;
use Maikovisky\LaravelPlus\PackagerHelper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Filesystem\Filesystem;

/**
 * Description of CrudNewCommand
 *
 * @author maiko
 */
class ScaffoldCommand extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {Name}';
    
     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a resourse controller with table, blade, repository';
    /**
     * Packager helper class.
     * @var object
     */
    protected $helper;
    
    protected $appPath;
    protected $resourcePath;
    protected $bar;
    protected $patterns = ['{{Name}}', '{{snake_name}}', '{{camel_name}}', 
        '{{camel_name_plural}}'];
    protected $patternsValues;
    protected $Name;
    protected $snake_name;
    protected $replaceFiles = ['index.blade.php', 'show.blade.php',
        'create.blade.php', 'edit.blade.php'];
    
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
    
    public function verifyFileExists($source) 
    {
        if(file_exists($source))
        {
            return !$this->confirm("File $source exist. Replace file?");
        }
        return false;
    }
    
    protected function modifyController() {

        $srcFile = __DIR__.'/stub/app/Http/Controllers/Controller_create.php';
        $ctlFile = app_path('Http/Controllers/' . str_plural($this->Name) . 'Controller.php');
        $file = new Filesystem();
        //$newSrc = preg_replace("/\s+\/\*\*\n.*Store/", $src . "\n\r$0", $oldSrc);
        
        $src = $file->get($srcFile);
        $oldSrc = $file->get($ctlFile);

        // Fix miss create
        $newSrc = preg_replace("/\s+\/\*\*\n.*Store/", "\n\r" .$src . "\n\r$0", $oldSrc);
        $newSrc = str_replace($this->patterns, $this->patternsValues, $newSrc);
        
        // Fix views
        $newSrc = str_replace($this->camel_name_plural . ".", $this->snake_name . ".", $newSrc);
        
        // Fix controller in update
        $newSrc = str_replace('update($id, $request->all())', 
                'update($request->all(), $id)' , $newSrc);
        $file->put($ctlFile, $newSrc);
        
    }
    
    protected function createBreadcrumbs() {
        
        $this->info('Add Breadcrumbs...');
        
        $srcFile = __DIR__.'/stub/routes/breadcrumbs.php';
        $breFile = base_path('routes/breadcrumbs.php');
        
        $file = new Filesystem();
        $src = $file->get($srcFile);
        $src = str_replace($this->patterns, $this->patternsValues, $src);
        
        $bre = $file->get($breFile);
        
        $file->put($breFile, $bre . $src);
        
    }
    
    protected function fixRequest()
    {
        $filename_create = app_path('Http/Requests/' . $this->Name . "CreateRequest.php");
        $filename_update = app_path('Http/Requests/' . $this->Name . "UpdateRequest.php");
        
        $this->helper->replaceAndSave($filename_create, "return false;",
                "return true;", $filename_create);
        
        $this->helper->replaceAndSave($filename_update, "return false;",
                "return true;", $filename_update);
    }

    protected function createView()
    {
        $this->info('Creating views...');
        
        $resourceBlank = 'views/blank';
        $resourcePath = 'views/' . $this->snake_name;
        $this->helper->makeDir(resource_path($resourcePath));
        
        foreach($this->replaceFiles as $file) {
            $newFile = resource_path($resourcePath . "/" . $file);
            if(!$this->verifyFileExists($newFile)) {
                $blank = resource_path($resourceBlank ."/" . $file);
                $stub  = __DIR__.'/stub/resource/views/' . $file;
                $changeFile = file_exists($blank) ? $blank : $stub;
                
                $this->helper->replaceAndSave($changeFile, $this->patterns,
                            $this->patternsValues, $newFile);
            }
        }
        
        $this->output->progressAdvance();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         // Start the progress bar
        $this->output->progressStart(5);
        
        $Name = $this->argument('Name');
        
        $this->snake_name = snake_case($Name);
        $this->camel_name = camel_case($Name);
        $this->camel_name_plural = str_plural(camel_case($Name));
        $this->Name       = $Name;
        
        $this->patternsValues = [$this->Name, $this->snake_name, 
            $this->camel_name, $this->camel_name_plural];
        
        
        $this->appPath      = getcwd() .'/app/';
        $this->resourcePath = getcwd() .'/resources/';

        $this->call('make:resource', ['name' => $Name]);
        $this->modifyController();
        $this->output->progressAdvance();
        
        $this->call('make:repository', ['name' => $Name]);
        $this->output->progressAdvance();
        
        $this->call('make:validator', ['name' => $Name]);
        $this->output->progressAdvance();
        
        $this->call('make:binding', ['name' => $Name]);
        $this->output->progressAdvance();
        
        $this->createBreadcrumbs();
        $this->fixRequest();
        $this->output->progressAdvance();  

		$this->createView();
		$this->info('Finish...');
    }
}
