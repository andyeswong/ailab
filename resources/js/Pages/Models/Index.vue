<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import {ref} from 'vue';
import {defineProps, watch, reactive} from 'vue';
import {Chart, registerables} from 'chart.js';
import pill from '@/Components/Pill.vue';
import primarybutton from '@/Components/PrimaryButton.vue';
import secondarybutton from '@/Components/SecondaryButton.vue';
import dangerbutton from '@/Components/DangerButton.vue';

Chart.register(...registerables);


const props = defineProps({
  user: Object,
});

const reactive_user = ref(props.user);
const model_console = ref({});
const showConsoleModal = ref(false);

var pusher = new Pusher('c6345026f4a44535826a', {
  cluster: 'us3'
});


var channel = pusher.subscribe(props.user.id.toString());

channel.bind("ai_model", function (data) {
  axios.get('/api/v1/models/user/' + props.user.id)
      .then(function (response) {
        reactive_user.value = response.data.data;
      })
});

watch(reactive_user, async (newValue, oldValue) => {
  console.log('reactive_user updated');
  if (model_console != undefined) {
    var model = reactive_user.value.models.find(x => x.id === model_console.value.id);
    model_console.value = model;
    fromMetricsToChart(JSON.parse(model.model_metrics));
  }
});

watch(model_console, async (newValue, oldValue) => {
  console.log('model_console updated');
});

var modelMetricsTrimmer = (model_metrics, length) => {
  if (model_metrics.length > length) {
    return model_metrics.substring(0, length) + '...';
  } else {
    return model_metrics;
  }
}

var modelMetricsLastObject = (model_metrics) => {
  var metrics = JSON.parse(model_metrics);
  if (metrics == undefined) {
    return {};
  } else if (metrics.length == 0) {
    return {};
  }
  var last_object = metrics[metrics.length - 1];
  return metrics[metrics.length - 1];
}

var openConsole = (model) => {
  model_console.value = model;
  showConsoleModal.value = true;
  fromMetricsToChart(JSON.parse(model.model_metrics));
}

var closeConsole = () => {
  showConsoleModal.value = false;
  // Get the context o f the canvas element we want to select
  var ctx = document.getElementById("console_chart").getContext("2d");
  // destroy chart
  Chart.getChart(ctx).destroy();

}

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
  if (Chart.getChart(ctx) != undefined) {
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




}

var deleteModel = (model) =>{
  axios.delete('/models/' + model.id)
      .then(function (response) {
        reactive_user.value = reactive_user.value.filter(x => x.id !== model.id);
      })
}


</script>

<template>
  <Head title="AIModels"/>

  <AuthenticatedLayout>
    <template #header>
      <div class="flex">
        <div class="flex-grow">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AI models</h2>
        </div>

        <!-- button to create Models -->
        <div class="flex-shrink-0">
          <primarybutton @click="goto('/models/create')">
            Create model
          </primarybutton>
        </div>
      </div>

    </template>

    <Transition>
      <div v-show="showConsoleModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
           role="dialog" aria-modal="true" x-show="showConsoleModal" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <!--
          Background overlay, show/hide based on modal state.

          Entering: "ease-out duration-300"
              From: "opacity-0"
              To: "opacity-100"
          Leaving: "ease-in duration-200"
              From: "opacity-100"
              To: "opacity-0"
      -->
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

          <!-- This element is to trick the browser into centering the modal contents. -->
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <!--
          Modal panel, show/hide based on modal state.

          Entering: "ease-out duration-300"
              From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              To: "opacity-100 translate-y-0 sm:scale-100"
          Leaving: "ease-in duration-200"
              From: "opacity-100 translate-y-0 sm:scale-100"
              To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
          <div
              class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
              role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <!-- close button -->
                  <button class="absolute top-0 right-0 mt-4 mr-4" @click="closeConsole()">
                    <svg class="h-6 w-6 text-gray-700 dark:text-gray-200" x-show="showConsoleModal"
                         x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                      id="modal-title">{{ model_console.model_name }} Training console</h3>
                  <div class="mt-2">
                    <canvas id="console_chart" width="400" height="400"></canvas>
                  </div>
                  <!-- div console style with                                         <p class="text-sm text-gray-500 dark:text-gray-400">
                          step: {{ metric.step }} | epoch: {{ metric.epoch }} | loss: {{ metric.loss }} | lr: {{ metric.learning_rate }}
                      </p> -->
                  <div class="w-full" v-if="model_console.model_metrics != undefined">
                    <div class="coding inverse-toggle px-5 pt-4 shadow-lg text-gray-100 text-sm font-mono subpixel-antialiased
              bg-gray-800  pb-6 pt-4 rounded-lg leading-normal overflow-hidden">
                      <div class="top mb-2 flex">
                        steps:
                      </div>
                      <div class="mt-4 flex"
                           v-for="metric in JSON.parse(model_console.model_metrics)">
                                                <span class="text-green-400">model_{{ model_console.model_name }}(step {{
                                                    metric.step
                                                  }}):</span>
                        <p class="flex-1 typing items-center pl-2">
                          epoch: {{ metric.epoch }} | loss: {{ metric.loss }} | lr: {{
                            metric.learning_rate
                          }}
                          <br>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button @click="closeConsole()"
                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-blue-500 text-base leading-6 font-medium text-white shadow-sm hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                    Close
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </Transition>


    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
             v-if="reactive_user.models != undefined && reactive_user.models.length > 0">
          <div class="p-6 text-gray-900 dark:text-gray-100" v-for="model in reactive_user.models">
            <div class="flex">
              <div class="flex-grow">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                    v-text="model.model_name"></h3>
                <p class="text-gray-600 dark:text-gray-400" v-text="model.model_description"></p>
                <p class="text-gray-600 dark:text-gray-400">
                  <!-- tailwind pill  -->
                  <span v-if="modelMetricsLastObject(model.model_metrics).train_loss != undefined">
                                        <pill :left="{text: 'train_loss', color: 'gray-800'}"
                                              :right="{text: modelMetricsLastObject(model.model_metrics).train_loss, color: 'gray-500'}"></pill>

                  </span>

                  <!-- tailwind pill  -->
                  <div v-if="model.model_hyperparameters != undefined">
                    <pill v-for="hyperparameter in hyperParametersToArray(model.model_hyperparameters)"
                          :left="{text: hyperparameter.name, color: 'gray-800'}"
                          :right="{text: hyperparameter.value, color: 'gray-500'}"></pill>
                  </div>
                </p>
              </div>
              <div class="flex-shrink-0">
                <secondarybutton class="mr-1" v-if="model.status == 'trained'" :href="`/models/${model.id}`">
                  Explore
                </secondarybutton>
                <secondarybutton class="mr-1" v-else>
                  {{ model.status }}
                </secondarybutton>
                <primarybutton @click="openConsole(model)">
                  Training console
                </primarybutton>
                <dangerbutton class="ml-1" @click="deleteModel(model)">
                  Delete
                </dangerbutton>
              </div>
            </div>
          </div>
        </div>
        <!-- v-else -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" v-else>
          <div class="p-6 text-gray-900 dark:text-gray-100">You have no models created.
            <a href="/models/create" class="text-blue-500 hover:text-blue-700">Create a model</a> to get
            started.
          </div>
        </div>
      </div>
    </div>
    <!-- show skeleton or loader -->

  </AuthenticatedLayout>
</template>
<style>
/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
