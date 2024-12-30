@if(isset($finalyear) && $finalyear != "" && $finalyear != '[]' && !empty($finalyear))

        @if(isset($chart) && $chart == 1)
        <div class="col-lg-12 grid-margin stretch-card bar-chart">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Bar chart</h4>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        @else 
        <div class="col-lg-12 grid-margin stretch-card pie-chart" id="pie-chart">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pie chart</h4>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
@endif

@else 

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Bar chart</h4>
                    <h2> No record Found !.. </h2>
                </div>
            </div>
        </div>

@endif


