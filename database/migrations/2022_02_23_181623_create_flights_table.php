<?php

use App\Models\Roster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('day')->nullable();
            $table->string('rev')->comment('revision')->nullable();
            $table->string('dc')->nullable();
            $table->string('c/i(l)')->comment('C/I(L)')->nullable();
            $table->string('c/i(z)')->comment('C/I(Z)')->nullable();
            $table->string('c/o(l)')->comment('C/O(L)')->nullable();
            $table->string('c/o(z)')->comment('C/O(Z)')->nullable();
            $table->string('activity')->nullable();
            $table->string('remark')->nullable();
            $table->string('from')->nullable();
            $table->string('std(l)')->comment('STD(L)')->nullable();
            $table->string('std(z)')->comment('STD(Z)')->nullable();
            $table->string('to')->nullable();
            $table->string('sta(l)')->comment('STA(L)')->nullable();
            $table->string('sta(z)')->comment('STA(Z)')->nullable();
            $table->string('ac/hotel')->nullable();
            $table->string('blh')->comment('Block hours')->nullable();
            $table->string('flight_time')->nullable();
            $table->string('night_time')->nullable();
            $table->string('dur')->nullable();
            $table->string('ext')->nullable();
            $table->string('pax_booked')->nullable();
            $table->string('acreg')->comment('Tail number')->nullable();
            $table->string('crewmeal')->nullable();
            $table->string('resources')->nullable();
            $table->string('cc')->comment('Crew code list')->nullable();
            $table->string('name')->comment('Full name list')->nullable();
            $table->string('pos')->comment('Position list')->nullable();
            $table->string('work_phone')->comment('Business phone list')->nullable();
            $table->string('dh_crew')->comment('Other DH crew code')->nullable();
            $table->string('dh_name')->comment('DH full name list')->nullable();
            $table->string('dh_seat')->comment('DH seating list')->nullable();
            $table->string('remarks')->nullable();
            $table->string('fdp_time')->comment('Actual FDP time')->nullable();
            $table->string('max_fdp')->comment('Max FDP time')->nullable();
            $table->string('rest_compl')->comment('Rest completed time')->nullable();
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
        Schema::dropIfExists('flights');
    }
}
