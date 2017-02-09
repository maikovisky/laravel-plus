<?php

namespace Maikovisky\LaravelPlus;

use Illuminate\Console\Command;
use Maikovisky\LaravelPlus\PackagerHelper;
use Illuminate\Support\Facades\Schema;


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
    protected $patterns = ['{{Name}}', '{{snake_name}}', '{{camel_name}}'];
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
    
    protected function createController() 
    {
        $this->info('Creating controller...');
        
        $controllerPath = $this->appPath . 'Http/Controllers';
        $newController  = $controllerPath.'/'.$this->crudName.'Controller.php';
        
        if(!$this->verifyFileExists($newController)) {       
            $this->helper->replaceAndSave(__DIR__.'/stub/app/Http/Controllers/Controller.stub', 
                    $this->patterns,  $this->patternsValues, $newController);
        }
        
        $this->output->progressAdvance();

    }
    
    protected function createRepository()
    {
        
        $this->info('Creating repository...');
        
        $repositoriesPath = $this->appPath . 'Repositories';
        $this->helper->makeDir($repositoriesPath);
        
        $newRepository = $repositoriesPath.'/'.$this->crudName.'Repository.php';
        $newRepositoryEloquent = $repositoriesPath.'/'.$this->crudName.'RepositoryEloquent.php';
        
        if(!$this->verifyFileExists($newRepository)) {
           $this->helper->replaceAndSave(__DIR__.'/stub/app/Repositories/Repository.stub', 
                    $this->patterns,  $this->patternsValues, $newRepository); 
        }
        
        if(!$this->verifyFileExists($newRepositoryEloquent)) {
            $this->helper->replaceAndSave(__DIR__.'/stub/app/Repositories/RepositoryEloquent.stub', 
                    $this->patterns,  $this->patternsValues, $newRepositoryEloquent);
        }
        
        $this->output->progressAdvance();
    }

    protected function createEntity()
    {
        $tableName = $this->argument('tableName');
        $columns = Schema::getColumnListing($tableName);
        $entitiesPath = $this->appPath . 'Entities';
        
        $this->info('Creating entity...');
        $this->helper->makeDir($entitiesPath);
        $newEntity = $entitiesPath.'/'.$this->crudName.'.php';
        
        $columns = array_diff($columns, ['id']);
        
        // Verify if exist softdelete in table
        if(in_array('deleted_at', $columns)) 
        {
           $this->info('Softdelete detected...');
           $columns = array_diff($columns, ['deleted_at']);
           $softDelete = 'TransformableTrait, SoftDeletes';
        }
        else 
        {
            $softDelete = 'TransformableTrait';
        }
        
        if(in_array('created_at', $columns)) {
           $this->info('Created and updated control detected...');
           $columns = array_diff($columns, ['created_at', 'updated_at']);
           $timestamps = '$timestamps = true';
        }
        else {
           $timestamps = '$timestamps = false';
        }
        
        if(in_array('password', $columns))
        {
            $columns = array_diff($columns, ['remember_token']);
            $hidden  = 'protected $hidden = [\'password\', \'remember_token\'];';
        }

        $fillable = "\$fillable = ['". implode("','",$columns) . "']";
        if(!$this->verifyFileExists($newEntity)) {
            $this->helper->replaceAndSave(__DIR__.'/stub/app/Entities/Entity.stub', 
                 $this->patterns,  $this->patternsValues, $newEntity);
            $this->helper->replaceAndSave($newEntity, '$fillable', $fillable, $newEntity);
            $this->helper->replaceAndSave($newEntity, '$timestamps = false', $timestamps, $newEntity);
            $this->helper->replaceAndSave($newEntity, 'TransformableTrait', $softDelete, $newEntity);
        }
        $this->output->progressAdvance();
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
        $this->Name       = $Name;
        
        $this->patternsValues = [$this->Name, $this->snake_name, 
            $this->camel_name];
        
        $this->appPath      = getcwd() .'/app/';
        $this->resourcePath = getcwd() .'/resources/';

        $this->createView();
        return;
//        $this->call('make:resource', ['name' => $Name]);
        $this->output->progressAdvance();
//        
//        $this->call('make:repository', ['name' => $Name]);
        $this->output->progressAdvance();
//        
//        $this->call('make:validator', ['name' => $Name]);
        $this->output->progressAdvance();
//        
//        $this->call('make:binding', ['name' => $Name]);
        $this->output->progressAdvance();
        $this->call('make:migration', ['--create' => str_plural($name), 
            'name' => "create_" . $name ."_table"]);
        $this->output->progressAdvance();
        return;
        $this->createController();
        $this->createRepository();  
        $this->createEntity(); 
        $this->createView();
        $this->info('Finish...');
    }
}
