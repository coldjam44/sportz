<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('addproducts', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false);
        });

        Schema::table('createstores', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false);
        });

        Schema::table('createstadium', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('addproducts', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });

        Schema::table('createstores', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });

        Schema::table('createstadiums', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
};
