<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuLanguage extends Model
{
    use HasFactory;

    protected $table = 'menu_language';

    // public function menus(){
    //     return $this->belongsTo(Menu::class, 'menu_id', 'id');
    // }
}
