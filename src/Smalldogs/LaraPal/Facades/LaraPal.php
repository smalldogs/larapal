<?php namespace Smalldogs\LaraPal\Facades;

use Illuminate\Support\Facades\Facade;

class LaraPal extends Facade {

    protected static function getFacadeAccessor() { return 'larapal'; }

    public function test() {}

}