<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('acl.table_names');

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_default')->nullable();
            $table->text('permissions')->nullable();
            $table->integer('access_level')->unsigned();
            $table->integer('access_level_parent')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create($tableNames['users_roles'], function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->timestamps();
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('acl.table_names');

        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['users_roles']);
    }
}
