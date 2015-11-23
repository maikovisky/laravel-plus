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
class CrudNewCommand extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:new {Name} {tableName}';
    
     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD.';
    /**
     * Packager helper class.
     * @var object
     */
    protected $helper;
    
    protected $appPath;
    protected $resourcePath;
    protected $bar;
    protected $patterns = ['{{Name}}', '{{name}}'];
    protected $patternsValues;
    protected $crudName;
    
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
    
    protected function createController() 
    {
        $this->info('Creating controller...');
        
        $controllerPath = $this->appPath . 'Http/Controllers';
        $newController  = $controllerPath.'/'.$this->crudName.'Controller.php';
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Http/Controllers/Controller.stub', 
                $this->patterns,  $this->patternsValues, $newController);
        
        $this->bar->advance();
    }
    
    protected function createRepository()
    {
        
        $this->info('Creating repository...');
        
        $repositoriesPath = $this->appPath . 'Repositories';
        $this->helper->makeDir($repositoriesPath);
        
        $newRepository = $repositoriesPath.'/'.$this->crudName.'Repository.php';
        $newRepositoryEloquent = $repositoriesPath.'/'.$this->crudName.'RepositoryEloquent.php';
        
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Repositories/Repository.stub', 
                $this->patterns,  $this->patternsValues, $newRepository);
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Repositories/RepositoryEloquent.stub', 
                $this->patterns,  $this->patternsValues, $newRepositoryEloquent);
        
        $this->bar->advance();
    }

    protected function createEntity()
    {
        $tableName = $this->argument('tableName');
        $columns = Schema::getColumnListing($tableName);
        $entitiesPath = $this->appPath . 'Entities';
        
        $this->info('Creating entity...');
        $this->helper->makeDir($entitiesPath);
        $newEntity = $entitiesPath.'/'.$this->crudName.'.php';
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Entities/Entity.stub', 
                 $this->patterns,  $this->patternsValues, $newEntity);
        
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
        $this->helper->replaceAndSave($newEntity, '$fillable', $fillable, $newEntity);
        $this->helper->replaceAndSave($newEntity, '$timestamps = false', $timestamps, $newEntity);
        $this->helper->replaceAndSave($newEntity, 'TransformableTrait', $softDelete, $newEntity);
        $this->bar->advance();
    }

    protected function createView()
    {
        $this->info('Creating views...');
        
        $resourcePath = $this->resourcePath . 'views/' . strtolower($this->crudName);
        $this->helper->makeDir($resourcePath);
        $newIndexView = $resourcePath .'/index.blade.php';
        $newEditView  = $resourcePath .'/edit.blade.php';
        
        $this->helper->replaceAndSave(__DIR__.'/stub/resources/views/index.blade.stub', 
                $this->patterns,  $this->patternsValues, $newIndexView);
        $this->helper->replaceAndSave(__DIR__.'/stub/resources/views/edit.blade.stub', 
                $this->patterns,  $this->patternsValues, $newEditView);
        $this->bar->advance();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         // Start the progress bar
        $this->bar = $this->helper->barSetup($this->output->createProgressBar(4));
        $this->bar->start();
        
        $Name = $this->argument('Name');
        $name = strtolower($Name);
        $this->crudName = $Name;
        
        $this->patternsValues = [$Name, $name];
        
        $this->appPath      = getcwd() .'/app/';
        $this->resourcePath = getcwd() .'/resources/';
       
        $this->createController();
        $this->createRepository();  
        $this->createEntity(); 
        $this->createView();
        $this->info('Finish...');
    }
}
