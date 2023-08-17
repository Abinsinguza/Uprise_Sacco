@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'UPRISE SACCO Performance Measurement and monitoring System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="dash">
        <div class="c ">
            <!-- notification -->
            <div class="card">
             @if (session('status'))
                <div style="background-color: #c3f1c5; color: #272424; padding: 5px; border-radius: 4px; font-size: 10px;  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                 {{ session('status') }}
                </div>
             @endif
                
            </div>
      <!-- Main -->
      <div class="main-cards-wrapper">
        <div class="card">
          <!-- ... Card content ... -->
          <div class="cardd ">
            <p>Total members</p>
              <span class="material-icons-outlined">{{ $totalMembers }}</span>
          </div>
          
        </div>
        <div class="card">
            <div class="cardd">
                <!-- ... Card content ... -->
                <p>Total Deposits</p>
                <span class="material-icons-outlined">UGX,{{ number_format($totalAmount, 2)   }}M</span>
              </div>
        </div>
        
        <div class="card">
            <div class="cardd">
                <!-- ... Card content ... -->
                <p>Total Contribution</p>
                <span class="material-icons-outlined">UGX,{{ number_format($totalContribution, 2 ) }}M</span>
              </div>
        </div>
        <div class="card">
            <div class="cardd">
                <!-- ... Card content ... -->
                <p>Perfomance </p>
                <span class="material-icons-outlined">{{ number_format($overallPerformance, 1) }}%</span>
              </div>
        </div>
        <div class="card">
            <div class="cardd">
                <!-- ... Card content ... -->
                <p>Avaliable Funds</p>
                <span class="material-icons-outlined">UGX,{{  number_format($availableFunds,2 )}}M</span>
              </div>
        </div>
       
      </div>
      <!-- End Main -->
    
        </div>
      
       
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card ">
                        
                        <div class="card-header ">
                            <h4 class="card-title">{{ __('Members Statistics') }}</h4>
                            <p class="card-category">{{ __('Last Campaign Performance') }}</p>
                        </div>

                        <div class="piechart">
                            <div id="donut-chart" class="ct-chart ct-perfect-fourth">
                                <style>
                                    .bb-chart-title {
                                        font-weight: bold;
                                    }
                                </style>
                              
                                <script src="path/to/billboard.min.js"></script>

                        </div>
                        <div class="card-body ">
                            
                            
                                <script>

                                    var totalMembers = {{ $totalMembers }};
                                    let chart = bb.generate({
                                        data: {
                                            columns: [
                                                ["Active", {{$activeMembers_count}}],
                                                ["New", {{$newMembers_count}}],
                                                ["Average", {{$dem_count}}],
                                            ],
                                            type: "donut",
                                            onclick: function (d, i) {
                                                console.log("onclick", d, i);
                                            },
                                            onover: function (d, i) {
                                                console.log("onover", d, i);
                                            },
                                            onout: function (d, i) {
                                                console.log("onout", d, i);
                                            },
                                        },
                                        donut: {
                                            title: "Total: " + totalMembers,
                                        },
                                        bindto: "#donut-chart",
                                    });


                        
                                </script>
                                
                            </div>
                            
                            
                            <div class="legend">
                                {{-- <i class="fa fa-circle text-info"></i> {{ __('Open') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('Bounce') }}
                                <i class="fa fa-circle text-warning"></i> {{ __('Unsubscribe') }} --}}
                            </div>
                            <hr>
                            <div class="stats">
                                <i class="fa fa-clock-o"></i> {{ __('Campaign sent 2 days ago') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">{{ __('SACCO PERFORMANCE') }}</h4>
                            <p class="card-category">{{ __('Monthly performance') }}</p>
                        </div>
                        <div class="card-body ">
                            <div id="chartHours" class="ct-chart">
    <div class="linechart">
        <canvas id="myChart"></canvas> 
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Calculate the past six months dynamically
        var today = new Date();
        var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var labels = [];
    
        for (var i = 5; i >= 0; i--) {
            var pastMonth = new Date(today.getFullYear(), today.getMonth() - i, 1);
            labels.push(monthNames[pastMonth.getMonth()] + ' ' + pastMonth.getFullYear());
        }
    
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Use the dynamically calculated month labels
                datasets: [{
                    label: 'Performance Over Months',
                    data: [30, 20, 40, 70, 100, {{ $overallPerformance }}], // Assuming you passed the overall performance value
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

                            </div>
                        </div>
                        <div class="card-footer ">
                            <div class="legend">
                                {{-- <i class="fa fa-circle text-info"></i> {{ __('Open') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('Click') }}
                                <i class="fa fa-circle text-warning"></i> {{ __('Click Second Time') }} --}}
                            </div>
                            <hr>
                            <div class="stats">
                                <i class="fa fa-history"></i> {{ __('Updated 3 minutes ago') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">{{ __('2017 Sales') }}</h4>
                            <p class="card-category">{{ __('All products including Taxes') }}</p>
                        </div>
                        <div class="card-body ">
                            <div id="area-chart" class="ct-chart"></div>
                        </div>
                         <div>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
                            <script>
                                var areaChartOptions = {
                              series: [{
                                name: 'Purchase Orders',
                                data: [31, 40, 28, 51, 42, 109, 100]
                              }, {
                                name: 'Sales Orders',
                                data: [11, 32, 45, 32, 34, 52, 41]
                              }],
                              chart: {
                                height: 350,
                                type: 'area',
                                toolbar: {
                                  show: false,
                                },
                              },
                              colors: ["#4f35a1", "#246dec"],
                              dataLabels: {
                                enabled: false,
                              },
                              stroke: {
                                curve: 'smooth'
                              },
                              labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
                              markers: {
                                size: 0
                              },
                              yaxis: [
                                {
                                  title: {
                                    text: 'Purchase Orders',
                                  },
                                },
                                {
                                  opposite: true,
                                  title: {
                                    text: 'Sales Orders',
                                  },
                                },
                              ],
                              tooltip: {
                                shared: true,
                                intersect: false,
                              }
                            };
                            
                            var areaChart = new ApexCharts(document.querySelector("#area-chart"), areaChartOptions);
                            areaChart.render();
                            </script>
                            
                            
                        </div>
                        
                        <div class="card-footer ">
                            <div class="legend">
                                <i class="fa fa-circle text-info"></i> {{ __('Tesla Model S') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('BMW 5 Series') }}
                            </div>
                            <hr>
                            <div class="stats">
                                <i class="fa fa-check"></i> {{ __('Data information certified') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card  card-tasks">
                        <div class="card-header ">
                            <h4 class="card-title">{{ __('Tasks') }}</h4>
                            <p class="card-category">{{ __('Backend development') }}</p>
                        </div>
                        <div class="card-body ">
                            <div class="table-full-width">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="">
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ __('Sign contract for "What are conference organizers afraid of?"') }}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="" checked>
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ __('Lines From Great Russian Literature? Or E-mails From My Boss?') }}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="" checked>
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ __('Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit') }}
                                            </td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ __('Create 4 Invisible User Experiences you Never Knew About') }}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="">
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ __('Read "Following makes Medium better"') }}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="" disabled>
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ __('Unfollow 5 enemies from twitter') }}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" title="Edit Task" class="btn btn-info btn-simple btn-link">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-link">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <div class="stats">
                                <i class="now-ui-icons loader_refresh spin"></i> {{ __('Updated 3 minutes ago') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>


@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            // Javascript method's body can be found in assets/js/demos.js
            demo.initDashboardPageCharts();

            demo.showNotification();

        });
    </script>
@endpush

