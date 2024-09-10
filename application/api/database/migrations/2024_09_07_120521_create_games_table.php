<?php

use App\Enums\Games\GameStatusesEnum;
use App\Models\League;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(League::class);
            $table->foreignIdFor(Team::class, 'home_team_id');
            $table->foreignIdFor(Team::class, 'guest_team_id');
            $table->integer('week');
            $table->json('scores')->nullable();
            $table->enum('status', [
                GameStatusesEnum::SCHEDULED->value,
                GameStatusesEnum::PLAYING->value,
                GameStatusesEnum::FINISHED->value,
            ])->default(GameStatusesEnum::SCHEDULED->value);
            $table->integer('game_time')->default(90);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
