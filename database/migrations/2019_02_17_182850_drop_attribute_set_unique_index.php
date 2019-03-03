<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAttributeSetUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_attributes', function (Blueprint $table) {
            $table->dropUnique(['attribute_set_id', 'attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entity_attributes', function (Blueprint $table) {
            $table->unique(['attribute_set_id', 'attribute_id']);
        });
    }
}
