<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maikovisky\LaravelPlus;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Description of PlusBaseModel
 *
 * @author maiko
 */
class PlusBaseModel extends Model{
    
    public static function boot()
    {
        parent::boot();
        
        static::creating(function($model)
        {
            $user = Auth::user();            
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });
        
        static::updating(function($model)
        {
            $user = Auth::user();
            $model->updated_by = $user->id;
        });        
        
        static::deleting(function($model)
        {
            $user = Auth::user();
            $model->deleted_by = $user->id;
        });  
    }
    
}
