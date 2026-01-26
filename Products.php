<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'tbl_products';
    public $timestamps = false;
}