<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loans</title>
    <Style>
        .linechart{
            width: 800px;
        }
    </Style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Loan ID</th>
                                <th>Member ID</th>
                                <th>Amount To Pay(UGX)</th>
                                <th>Amount Per Installment(UGX)</th>
                                <th>Period(months)</th>
                                <th>Start Date</th>
                                <th>Progress</th>
                                <th>Cleared Amount(UGX)</th>
                                <th>Loan Balance(UGX)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($all_loans as $data)
                                <tr>
                                    <td>{{ $data->loanId }}</td>
                                    <td>{{ $data->memberId }}</td>
                                    <td>{{ number_format($data->amountToPay, 2) }}</td>
                                    <td>{{ number_format($data->amountPerInstallment, 2) }}</td>
                                    <td>{{ $data->installments }}</td>
                                    <td>{{ $data->paymentStart }}</td>
                                    <td>{{ $data->loanProgress }}</td>
                                    <td>{{ number_format($data->amountCleared, 2) }}</td>
                                    <td>{{ number_format($data->loanBalance, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <div class="linechart">
        <canvas id="myChart"></canvas> 
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'Performance Over Months',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        </script>
</body>

</html>