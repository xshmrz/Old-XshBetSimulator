<?php
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    return new class extends Migration {
        /**
         * Run the migrations.
         * @return void
         */
        public function up() {
            Schema::create('user', function (Blueprint $table) {
                $table->id();
                $table->string('username')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            Schema::create('bet', function (Blueprint $table) {
                $table->id();
                # ->
                $table->tinyInteger('addedToCoupon')->nullable()->default(EnumProjectAddedToCoupon::No);
                $table->string('score')->nullable();
                $table->tinyInteger('status')->nullable()->default(EnumProjectStatus::Pending);
                $table->tinyInteger('finish')->nullable()->default(EnumProjectFinish::No);
                $table->tinyInteger('live')->nullable()->default(EnumProjectLive::No);
                # ->
                $table->string('populerBetId')->nullable();
                $table->string('sportId')->nullable();
                $table->string('eventId')->nullable();
                $table->string('eventName')->nullable();
                $table->string('eventDate')->nullable();
                $table->string('marketNo')->nullable();
                $table->string('marketVersion')->nullable();
                $table->string('outcomeNo')->nullable();
                $table->string('outcomeName')->nullable();
                $table->string('odd')->nullable();
                $table->string('showOdd')->nullable();
                $table->string('minimumBetCount')->nullable();
                $table->string('competitionId')->nullable();
                $table->string('competitionAcronym')->nullable();
                $table->string('competitionName')->nullable();
                $table->string('countryId')->nullable();
                $table->string('betradarId')->nullable();
                $table->string('isKingBet')->nullable();
                $table->string('isKingLive')->nullable();
                $table->string('isKingOdd')->nullable();
                $table->string('isKingMbs')->nullable();
                $table->string('isLive')->nullable();
                $table->string('playedRatio')->nullable();
                $table->string('isDeleted')->nullable();
                $table->string('marketType')->nullable();
                $table->string('marketSubType')->nullable();
                $table->string('marketId')->nullable();
                $table->string('marketName')->nullable();
                $table->string('webOdd')->nullable();
                $table->string('totalPlayed')->nullable();
                $table->string('totalPlayedRoundStr')->nullable();
                $table->string('longEventDate')->nullable();
                # ->
                $table->timestamps();
                $table->softDeletes();
            });
            Schema::create('coupon', function (Blueprint $table) {
                $table->id();
                $table->string('no')->nullable();
                $table->tinyInteger('status')->nullable();
                $table->string('data')->nullable();
                $table->double('odd', 12, 2)->nullable();
                $table->tinyInteger('finish')->nullable()->default(EnumProjectFinish::No);
                $table->tinyInteger('live')->nullable()->default(EnumProjectLive::No);
                $table->timestamps();
                $table->softDeletes();
            });
        }
        /**
         * Reverse the migrations.
         * @return void
         */
        public function down() {
            Schema::dropIfExists('user');
            Schema::dropIfExists('bet');
        }
    };
