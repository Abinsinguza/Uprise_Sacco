<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loanrequests', function (Blueprint $table) {
            $table->id();
            $table->string('referenceNumber')->unique();
            $table->string('memberId');
            $table->decimal('loan_amount', 10, 2);
            $table->unsignedInteger('periodInMonths');
            //$table->decimal('loan_amount_limit', 10, 2)->nullable();
            $table->string('approval')->default('pending');
            $table->string('clientChoice')->default('pending');
            $table->decimal('avPerformance', 10, 2)->default(0.00);
            $table->unsignedInteger('totalContributions')->default(0);
            $table->decimal('monthlyContribution', 10, 2)->default(0.00);
            $table->timestamps();
        });
        DB::unprepared('
            CREATE TRIGGER tr_generate_referenceNumber BEFORE INSERT ON loanrequests
            FOR EACH ROW
            BEGIN
                DECLARE lastReferenceNumber INT;
                SET lastReferenceNumber = COALESCE((SELECT MAX(CAST(SUBSTRING(referenceNumber, 3) AS UNSIGNED)) FROM loanrequests), 0);
                SET NEW.referenceNumber = CONCAT("LR", LPAD(lastReferenceNumber + 1, 4, "0"));
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER `rank_loan_insert` BEFORE INSERT ON `loanrequests`
            FOR EACH ROW BEGIN
              DECLARE calculated_avPerformance DECIMAL(10, 2);
              DECLARE calculated_con_performance DECIMAL(10, 2);
              DECLARE calculated_loan_performance DECIMAL(10, 2);
              DECLARE calculated_con_per_month DECIMAL(10, 2);
              DECLARE cleared_installments INT;
              DECLARE con_amount DECIMAL(10, 2);
          
              -- Retrieve data from members based on memberId
              SELECT contributionPerMonth, performance INTO calculated_con_per_month, calculated_con_performance
              FROM members
              WHERE memberId = NEW.memberId;
          
              -- Retrieve data from loans based on memberId
              SELECT performance INTO calculated_loan_performance
              FROM loans
              WHERE memberId = NEW.memberId;
          
              -- Getting number of deposits from table deposits
              SELECT COUNT(*) INTO cleared_installments 
              FROM deposits 
              WHERE memberId = NEW.memberId;
          
              -- Calculate the average performance using the formula
              IF calculated_loan_performance IS NULL THEN
                SET NEW.avPerformance = calculated_con_performance;
              ELSEIF calculated_loan_performance IS NOT NULL THEN  
                SET NEW.avPerformance = (calculated_con_performance + calculated_loan_performance) * 0.5;
              ELSE 
                SET NEW.avPerformance = 0;
              END IF;
          
              -- Set the monthlyContribution from members
              SET NEW.monthlyContribution = calculated_con_per_month; 
          
              -- Set the totalContributions from deposits
              SET NEW.totalContributions = cleared_installments;
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
        Schema::dropIfExists('loanrequests');
    }
}
