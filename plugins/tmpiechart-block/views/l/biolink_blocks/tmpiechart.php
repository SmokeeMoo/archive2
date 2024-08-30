<?php defined('ALTUMCODE') || die() ?>

<style>

.chart {
  position: relative;
  width: 100%;
}

@media (min-width:801px)  {
 .chart {
  position: relative;
  width: 70%;
  margin: 0 auto;
}   

.card {
 width: 70%;
 margin: 0 auto;   
}
}

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    
    <div class="card text-center shadow" style="border: 0px solid; background:<?= $data->link->settings->background_color ?>;">
        <div class="card-header text-center" style="color: <?= $data->link->settings->text_color ?>; border-bottom: 1px solid #f2f2f247; font-weight: bold;"><?= $data->link->settings->title_block ?></div>
        <div class="card-body">
    <div class="chart">
  <canvas id="pie-chart"></canvas>
</div>

</div>
</div>

<script>
    
    const ctx = document.querySelector("#pie-chart");

const labels = [<?php foreach($data->link->settings->items as $key => $item): ?>"<?= $item->title ?>", <?php endforeach ?>];

const data = {
  labels,
  datasets: [
    {
      data: [<?php foreach($data->link->settings->items as $key => $item): ?><?= $item->content ?>, <?php endforeach ?>],
      label: "Favourite Colour",
      backgroundColor: [<?php foreach($data->link->settings->items as $key => $item): ?>"<?= $item->color ?>", <?php endforeach ?>],
      hoverOffset: 3,
      borderWidth: 3,
      borderColor: '<?= $data->link->settings->background_color ?>'
    }
  ]
};

const config = {
  type: "pie",
  data,
  options: {
    responsive: true,

    plugins: {

      tooltip: {
        titleFont: { weight: "light" }
      },

      legend: {
        position: "bottom",
        labels: {color: "<?= $data->link->settings->text_color ?>"}
      }
    }
  }
};

const lineChart = new Chart(ctx, config);

</script>
    
 
</div>
