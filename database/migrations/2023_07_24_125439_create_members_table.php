<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('memberId', 4)->unique();
            $table->string('email', 25);
            $table->string('name', 30);
            $table->string('username', 15);
            $table->string('password', 25);
            $table->string('phoneNumber', 15);
            $table->decimal('balance', 10, 2)->default(0);
            $table->date('contributionStart')->nullable();
            $table->decimal('contributionPerMonth', 10, 2);
            $table->integer('monthsCleared')->nullable();
            $table->decimal('performance', 6, 2)->nullable();
            $table->timestamps();
        });

        DB::unprepared('
            CREATE TRIGGER set_memberId
            BEFORE INSERT ON members
            FOR EACH ROW
            BEGIN
                SET NEW.memberId = CONCAT("M", LPAD((SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "members"), 3, "0"));
            END;
        ');

        DB::unprepared('
        CREATE TRIGGER update_member_performance_insert
        BEFORE INSERT ON members
        FOR EACH ROW
        BEGIN
            SET NEW.monthsCleared = GREATEST(0, FLOOR(NEW.balance / NEW.contributionPerMonth));
            SET @totalMonths = PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM CURRENT_DATE()), EXTRACT(YEAR_MONTH FROM NEW.contributionStart));
            IF @totalMonths > 0 THEN
                SET NEW.performance = NEW.monthsCleared * 100 / @totalMonths;
            END IF;
        END;
    ');

    DB::unprepared('
        CREATE TRIGGER update_member_performance_update
        BEFORE UPDATE ON members
        FOR EACH ROW
        BEGIN
            SET NEW.monthsCleared = GREATEST(0, FLOOR(NEW.balance / NEW.contributionPerMonth));
            SET @totalMonths = PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM CURRENT_DATE()), EXTRACT(YEAR_MONTH FROM NEW.contributionStart));
            IF @totalMonths > 0 THEN
                SET NEW.performance = NEW.monthsCleared * 100 / @totalMonths;
            END IF;
        END;
    ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
