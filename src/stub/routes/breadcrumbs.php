
// Home > {{snake_name}}
Breadcrumbs::register('{{snake_name}}', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('general.{{Name}}'), route('{{snake_name}}.index'));
});

// Home > {{snake_name}} > Edit
Breadcrumbs::register('{{snake_name}}.edit', function($breadcrumbs, ${{camel_name}} = null)
{
    $breadcrumbs->parent('{{snake_name}}');
    if(isset(${{camel_name}}->id)) {
        $breadcrumbs->push(${{camel_name}}->name, route('{{snake_name}}.update', ${{camel_name}}->id));
    }
    else {
        $breadcrumbs->push(Lang::get("general.New"), route('{{snake_name}}.create'));
    }  
});
