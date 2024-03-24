<?php
use SLiMS\Migration\Migration;
use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateWaLog extends Migration
{
    function up()
    {
        Schema::create('whacenter_log', function(Blueprint $table) {
            $table->autoIncrement('id');
            $table->text('content')->notNull();
            $table->text('provider_response')->notNull();
            $table->datetime('created_at')->notNull();
            $table->fulltext('content');
            $table->fulltext('provider_response');
        });
    }

    function down()
    {

    }
}
