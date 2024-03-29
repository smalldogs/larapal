<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpnOrderItemOptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// If table already exists, attempt to ensure it is Eloquent ready
		if (Schema::hasTable('ipn_order_item_options'))
		{
			//
			Schema::table('ipn_order_item_options', function($table)
			{
				$table->integer('ipn_order_item_id');
				$table->nullableTimestamps();
				$table->softDeletes()->nullable();
			});
		}

		else
		{
			Schema::create('ipn_order_item_options', function (Blueprint $table)
			{
				$table->increments('id');
				$table->integer('ipn_order_item_id');
				$table->string('option_name', 64)->nullable();
				$table->string('option_selection', 200)->nullable();
				$table->timestamps();
				$table->softDeletes();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ipn_order_item_options');
	}

}