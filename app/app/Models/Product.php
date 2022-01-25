<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'desc',
        'qty',
        'price',
        'img',
        'availability'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Clear Products table
     */
    public static function clear()
    {
        self::query()->delete();
    }

    /**
     * Add new product
     * @param $data
     * @return Product
     */
    public static function add($data)
    {
        $product = new Product();
        $product->name = $data['name'];
        $product->desc = $data['desc'];
        $product->qty = $data['qty'];
        $product->price = $data['price'];
        $product->img = $data['img'];
        $product->availability = $data['availability'];
        $product->save();
        return $product;
    }

}
