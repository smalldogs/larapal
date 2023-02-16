<?php namespace Smalldogs\LaraPal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IpnOrderItemOption extends Model {

    use SoftDeletingTrait;

    protected $table = 'ipn_order_item_options';

    protected $dates = ['deleted_at'];

    protected $fillable = ['option_name', 'option_selection'];

    public function order()
    {
        return $this->belongsTo('Smalldogs\LaraPal\Models\IpnOrderItem');
    }

}