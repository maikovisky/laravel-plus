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
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         // Start the progress bar
        $bar = $this->helper->barSetup($this->output->createProgressBar(7));
        $bar->start();
        
        $Name      = $this->argument('Name');
        $tableName = $this->argument('tableName');
        $name      = strtolower($Name);
        
        
        $path = getcwd();
        $appPath = $path .'/app/';
        $controllerPath = $appPath . 'Http/Controllers';
        $entitiesPath = $appPath . 'Entities';
        $repositoriesPath = $appPath . 'Repositories';
        
        
        $this->info('Creating controller...');
        $newController = $controllerPath.'/'.$Name.'Controller.php';
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Http/Controllers/Controller.stub', ['{{Name}}', '{{name}}'], [$Name, $name], $newController);
        $bar->advance();
        
        $this->info('Creating repository...');
        $this->helper->makeDir($repositoriesPath);
        $newRepository = $repositoriesPath.'/'.$Name.'Repository.php';
        $newRepositoryEloquent = $repositoriesPath.'/'.$Name.'RepositoryEloquent.php';
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Repositories/Repository.stub', ['{{Name}}', '{{name}}'], [$Name, $name], $newRepository);
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Repositories/RepositoryEloquent.stub', ['{{Name}}', '{{name}}'], [$Name, $name], $newRepositoryEloquent);
        $bar->advance();
        
        $columns = Schema::getColumnListing($tableName);
        
        
        $this->info('Creating entity...');
        $this->helper->makeDir($entitiesPath);
        $newEntity = $entitiesPath.'/'.$Name.'.php';
        $this->helper->replaceAndSave(__DIR__.'/stub/app/Entities/Entity.stub', ['{{Name}}', '{{name}}'], [$Name, $name], $newEntity);
        
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
            $hidden = 'protected $hidden = [\'password\', \'remember_token\'];';
        }

        $fillable = "\$fillable = ['". implode("','",$columns) . "']";
        $this->info($fillable); 
        $this->helper->replaceAndSave($newEntity, '$fillable', $fillable, $newEntity);
        $this->helper->replaceAndSave($newEntity, '$timestamps = false', $timestamps, $newEntity);
        $this->helper->replaceAndSave($newEntity, 'TransformableTrait', $softDelete, $newEntity);
        
        dd($columns);
        $bar->advance();
    }
}
