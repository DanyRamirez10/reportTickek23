<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Button extends Model
{
    use HasFactory;
     protected $fillable = [
        'nombre',
        'icon',
        'class',
        'url_to',
    ];
}
