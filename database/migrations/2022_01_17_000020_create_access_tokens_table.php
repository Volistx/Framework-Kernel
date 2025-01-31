<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->ulid('id', 36)->primary();
            $table->string('key', 32)->index('access_token_key');
            $table->string('secret', 64);
            $table->string('secret_salt', 16);
            $table->integer('ip_rule');
            $table->json('ip_range')->default('[]');
            $table->integer('country_rule');
            $table->json('country_range')->default('[]');
            $table->json('permissions')->default('[]');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_tokens');
    }
};
