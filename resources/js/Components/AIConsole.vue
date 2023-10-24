<script setup>
import {defineProps, watch, ref} from 'vue';
import primarybutton from '@/Components/PrimaryButton.vue';
import {Chart, registerables} from 'chart.js';
import secondarybutton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    model: Object,
});


Chart.register(...registerables);

const chart_generated = ref(false);

var fromMetricsToChart = (model_metrics) => {
    let steps = [];
    let losses = [];
    let learningRates = [];
    let epochs = [];

    model_metrics.forEach(item => {
        if (item.step) {
            steps.push(item.step);
            losses.push(item.loss);
            learningRates.push(item.learning_rate);
            epochs.push(item.epoch);
        }
    });

    // Get the context of the canvas element we want to select
    var ctx = document.getElementById("console_chart").getContext("2d");
    // if chart already exists destroy it
    if (Chart.getChart(ctx) !== undefined) {
        Chart.getChart(ctx).destroy();
    }

    // Create Chart
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: epochs,
            datasets: [{
                label: 'Loss',
                data: losses,
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: false
            },
                {
                    label: 'Learning Rate',
                    data: learningRates,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Chart.js Line Chart - Multi Axis'
            },
            tooltips: {
                mode: 'index',
                intersect: true
            },
            scales: {
                yAxes: [{
                    type: 'linear',
                    display: true,
                    position: 'left',
                }]
            }
        }
    });

    chart_generated.value = true;

}


</script>

<template>
    <div class="bg-white dark:bg-gray-800 p-5 rounded-md">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <div >
                    <canvas id="console_chart"  v-show="chart_generated" ></canvas>
                    <div class=" text-center grid grid-1 align-middle p-5" v-if="!chart_generated">
                        <secondarybutton @click="fromMetricsToChart(JSON.parse(model.model_metrics))">Generate chart
                        </secondarybutton>
                    </div>
                </div>
                <div class="w-full max-h-56" v-if="model.model_metrics != undefined">
                    <div class="coding inverse-toggle p-5 text-gray-100 text-sm font-mono subpixel-antialiased
              bg-gray-800  pb-6 rounded-md leading-normal overflow-auto max-h-56">
                        <div class="top mb-2 flex">
                            steps:
                        </div>
                        <div class="mt-4 flex"
                             v-for="metric in JSON.parse(model.model_metrics)">
                                                <span class="text-green-400">(step {{
                                                        metric.step
                                                    }}):</span>
                            <p class="flex-1 typing items-center pl-2">
                                epoch: {{ metric.epoch }} | loss: {{ metric.loss }}
                                <!--                    | lr: {{-->
                                <!--                    metric.learning_rate-->
                                <!--                  }}-->
                                <br>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<style scoped>

</style>
