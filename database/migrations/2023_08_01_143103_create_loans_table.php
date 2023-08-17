<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loanId', 5)->unique();
            $table->string('memberId', 10);
            $table->string('referenceNumber', 10);
            $table->decimal('amount_given', 10, 2)->default(0.00);
            $table->decimal('amountToPay', 10, 2)->default(0.00);
            $table->date('paymentStart')->default('2000-01-01');
            $table->integer('installments')->default(0);
            $table->decimal('amountPerInstallment', 10, 2)->default(0.00);
            $table->decimal('amountCleared', 10, 2)->default(0.00);
            $table->integer('installmentCleared')->default(0);
            $table->decimal('loanBalance', 10, 2)->default(0.00);
            $table->decimal('loanProgress', 10, 2)->default(0.00);
            $table->decimal('performance', 5, 2)->default(0.00);
            $table->decimal('loanFine', 10, 2)->default(0.00);
            $table->integer('delayedInstallments')->default(0);
            $table->string('status')->nullable;
            $table->timestamps();
        });
        DB::unprepared('
        

        CREATE TRIGGER before_loan_insert BEFORE INSERT ON loans FOR EACH ROW 
        BEGIN
  DECLARE requested_installments INT;
  DECLARE member_id_value VARCHAR(5);
  DECLARE loan_amount_value DECIMAL(10, 2);
  DECLARE calculated_amount DECIMAL(10, 2);
  DECLARE calculated_amount_per_installment DECIMAL(10, 2);
  DECLARE calculated_installments_cleared INT;
  DECLARE calculated_loan_balance DECIMAL(10, 2);
  DECLARE calculated_performance DECIMAL(5, 2);
  DECLARE calculated_loan_fine DECIMAL(10, 2);
  DECLARE calculated_delayed_installments INT;
  

  -- Retrieve data from loanrequests based on referenceNumber
  SELECT memberId, loan_amount, periodInMonths INTO member_id_value, loan_amount_value, requested_installments
  FROM loanrequests
  WHERE referenceNumber = NEW.referenceNumber;

  -- Set the memberId, amount_given, and Installments directly for the new row being inserted
  SET NEW.memberId = member_id_value;
  SET NEW.amount_given = loan_amount_value;
  SET NEW.Installments = requested_installments;
   -- Calculate the amountToPay using the formula: ([ (5+installments)/100]*amount_given)+ amount_given
  SET calculated_amount = ((5 + requested_installments) / 100) * loan_amount_value + loan_amount_value;
  SET NEW.amountToPay = calculated_amount;
 -- Calculate the amountPerInstallment by dividing amountToPay by Installments
  SET calculated_amount_per_installment = calculated_amount / requested_installments;
  SET NEW.amountPerInstallment = calculated_amount_per_installment;
 
  -- Calculate Installment_cleared using the formula: FLOOR(amountCleared / amountPerInstallment)
  SET calculated_installments_cleared = FLOOR(NEW.AmountCleared / calculated_amount_per_installment);
  SET NEW.installmentCleared = calculated_installments_cleared; 
   -- Calculate the loanBalance by subtracting amountCleared from amountToPay
  SET calculated_loan_balance = calculated_amount - NEW.AmountCleared;
  SET NEW.loanBalance = calculated_loan_balance;
  
   SET NEW.loanProgress = (NEW.AmountCleared / NEW.amountToPay) * 100; 
       -- Set the status column based on loanProgress
    IF NEW.loanProgress >= 100 THEN
        SET NEW.status = "complete";
    ELSE
        SET NEW.status = "in progress";
    END IF;
  
  -- Calculate the performance using the formula: [installment/(current date-paymentStart)]* 100 if (current date-paymentStart) is else than one ..then performance is 100
  IF DATEDIFF(current_date, NEW.paymentStart) > 0 THEN
    SET calculated_performance = (NEW.InstallmentCleared / DATEDIFF(current_date, NEW.paymentStart)) * 100;
  ELSE
    SET calculated_performance = 100;
  END IF;
  SET NEW.performance = calculated_performance;
    -- Calculate the loanFine by increasing it by 10% of amountPerInstallment whenever performance reduces
  IF NEW.performance < 100 THEN
    SET calculated_loan_fine = NEW.loanFine + (0.1 * calculated_amount_per_installment);
  ELSE
    SET calculated_loan_fine = NEW.loanFine;
  END IF;
  SET NEW.loanFine = calculated_loan_fine;
    -- Calculate delayedInstallments by getting months diff between dates minus cleared installments
  SET calculated_delayed_installments = FLOOR(calculated_loan_fine / (0.1 * calculated_amount_per_installment));
  SET NEW.delayedInstallments = calculated_delayed_installments;
 

  -- Set paymentStart to one month from the current date
SET NEW.paymentStart = DATE_ADD(CURRENT_DATE, INTERVAL 1 MONTH);
END


    ;
    ');
       // Create the 'calculation_loan_update' trigger
       DB::unprepared('
       CREATE TRIGGER calculation_loan_update BEFORE UPDATE ON loans FOR EACH ROW 
           -- Add the trigger logic here (copy the entire trigger content from the previous response)
           BEGIN
  DECLARE calculated_installments_cleared INT;
  DECLARE calculated_loan_balance DECIMAL(10, 2);
  DECLARE calculated_performance DECIMAL(5, 2);
  DECLARE calculated_loan_fine DECIMAL(10, 2);
  DECLARE calculated_delayed_installments INT;
 

  -- Calculate Installment_cleared using the formula: FLOOR(amountCleared / amountPerInstallment)
  SET calculated_installments_cleared = FLOOR(NEW.AmountCleared / NEW.amountPerInstallment);
  SET NEW.installmentCleared = calculated_installments_cleared; 
  -- Calculate the loanBalance by subtracting amountCleared from amountToPay
  SET calculated_loan_balance = NEW.amountToPay - NEW.AmountCleared;
  SET NEW.loanBalance = calculated_loan_balance;
  
  -- calculate loan progress 
  SET NEW.loanProgress = (NEW.AmountCleared / NEW.amountToPay) * 100;
      -- Set the status column based on loanProgress
    IF NEW.loanProgress >= 100 THEN
        SET NEW.status = "complete";
    ELSE
        SET NEW.status = "in progress";
    END IF;
  
  -- Calculate the performance using the formula: [installment/(current date-paymentStart)]* 100 if (current date-paymentStart) is else than one ..then performance is 100
  IF DATEDIFF(current_date, NEW.paymentStart) > 0 THEN
    SET calculated_performance = (NEW.InstallmentCleared / DATEDIFF(current_date, NEW.paymentStart)) * 100;
  ELSE
    SET calculated_performance = 100;
  END IF;
  SET NEW.performance = calculated_performance;
  
  -- Calculate the loanFine by increasing it by 10% of amountPerInstallment whenever performance reduces
  IF NEW.performance < 100 THEN
    SET calculated_loan_fine = NEW.loanFine + (0.1 * NEW.amountPerInstallment);
  ELSE
    SET calculated_loan_fine = NEW.loanFine;
  END IF;
  SET NEW.loanFine = calculated_loan_fine;
  
  -- Calculate delayedInstallments by dividing loanFine by 10% of amountPerInstallment
  SET calculated_delayed_installments = FLOOR(NEW.loanFine / (0.1 * NEW.amountPerInstallment));
  SET NEW.delayedInstallments = calculated_delayed_installments;


       END;
   ');

   DB::unprepared('
   CREATE TRIGGER generate_loanId BEFORE INSERT ON loans FOR EACH ROW 
   BEGIN
       DECLARE lastLoanId INT;
       SELECT SUBSTRING(loanId, 2) INTO lastLoanId FROM loans ORDER BY loanId DESC LIMIT 1;
       SET NEW.loanId = CONCAT("L", LPAD(COALESCE(lastLoanId + 1, 1), 3, "0"));
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
        Schema::dropIfExists('loans');
    }
}
