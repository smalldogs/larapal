<?php namespace Smalldogs\LaraPal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IpnOrderItem extends Model {

    use SoftDeletingTrait;

    protected $table = 'ipn_order_items';

    protected $dates = ['deleted_at'];

    protected $fillable = ['item_name', 'item_number', 'item_number',
        'quantity', 'mc_gross', 'mc_handling', 'mc_shipping', 'tax'
    ];

    public function order()
    {
        return $this->belongsTo('Smalldogs\LaraPal\Models\IpnOrder');
    }

    public function options()
    {
        return $this->hasMany('Smalldogs\LaraPal\Models\IpnOrderItemOption');
    }

}