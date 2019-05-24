<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcategoria')->unsigned();
            $table->string('codigo',50)->nullable();
            $table->string('nombre',100)->nullable();
            $table->decimal('precio_insumo',11,2);
            $table->string('ubicacion',50)->nullable();
            $table->integer('stock');
            $table->boolean('condicion')->default(1);
            $table->timestamps();

            $table->foreign('idcategoria')->references('id')->on('categorias');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insumos');
    }
}
