<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcompra')->unsigned();
            $table->foreign('idcompra')->references('id')->on('compras')->onDelete('cascade');
            $table->integer('idinsumo')->unsigned();
            $table->foreign('idinsumo')->references('id')->on('insumos');
            $table->integer('cantidad');
            $table->decimal('precio_compra',11,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_compras');
    }
}
