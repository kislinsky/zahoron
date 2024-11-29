<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestsCostProductsSupplier extends Model
{
    use HasFactory;
    protected $guarded =[];

    function organizationProvider(){
        return Organization::find($this->organization_provider_id);
    }

    function findProduct($id_product){
        return Product::find($id_product);
    }

}