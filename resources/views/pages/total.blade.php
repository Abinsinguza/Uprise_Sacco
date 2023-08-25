<div class="card-body">
    <div id="area-chart" class="ct-chart"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
<script>
    var areaChartOptions = {
        series: [{
            name: 'Total Amount',
            data: [
                @foreach ($amountsByMonth as $amount)
                    {{ $amount['total_amount'] }},
                @endforeach
            ]
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false,
            },
        },
        colors: ["#4f35a1"],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: [
                @foreach ($amountsByMonth as $amount)
                    '{{ $amount['month'] }} {{ $amount['year'] }}',
                @endforeach
            ],
        },
        yaxis: {
            title: {
                text: 'Total Amount',
            },
        },
        markers: {
            size: 0
        },
        tooltip: {
            shared: true,
            intersect: false,
        }
    };

    var areaChart = new ApexCharts(document.querySelector("#area-chart"), areaChartOptions);
    areaChart.render();
</script>
