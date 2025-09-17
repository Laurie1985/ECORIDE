<?php
namespace App\Models;

class Brand extends BaseModel
{
    protected static $table      = 'brands';
    protected static $primaryKey = 'brand_id';

    /**
     * Trouve une marque par son nom.
     */
    public static function findByNameBrand(string $brandName): array | false
    {
        return self::findBy(['name_brand' => $brandName]);
    }
}
