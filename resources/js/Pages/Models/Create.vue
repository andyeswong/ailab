<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import {ref} from 'vue';
import primarybutton from '@/Components/PrimaryButton.vue';
import secondarybutton from '@/Components/SecondaryButton.vue';
import modal from '@/Components/Modal.vue';

const props = defineProps({
  user: Object,
});

const data_sets = props.user.data_sets;

const showStep1Modal = ref(false);
const showStep2Modal = ref(false);
const selectedDatasetID = ref(null);
const selectedDataset = ref(null);
const modelName = ref(null);

const trainingParams = {
  epochs: {type: "int", value: 3, disabled: false, max: 100, min: 1},
  batch_size: {type: "int", value: 1, disabled: false, max: 8, min: 1},
  learning_rate: {type: "float", value: 5e-5, disabled: false},
  base: {type: "string", value: "t5-small", disabled: true},
}


const selectDatasetModelModalToggle = () => {
  showStep1Modal.value = !showStep1Modal.value;
}

const updateDataset = (event) => {
  selectedDatasetID.value = event.target.value;
}

const selectDataset = () => {
  selectedDataset.value = data_sets.find(dataset => dataset.id == selectedDatasetID.value);
  showStep1Modal.value = false;
}

const configureParams = () => {
  showStep2Modal.value = !showStep2Modal.value;
}

const trainModel = () => {
  var params = {
    epochs: trainingParams.epochs.value,
    batch_size: trainingParams.batch_size.value,
    learning_rate: trainingParams.learning_rate.value,
    base: trainingParams.base.value,
    data_set_id: selectedDatasetID.value,
    model_name: modelName.value,
    model_description: "This is a model description",
    user_id: props.user.id,
  }

  axios.post('/api/v1/models', {params})
      .then(function (response) {
        console.log(response);
        window.location.href = "/models";
      })
      .catch(function (error) {
        console.log(error);
      });
}


</script>

<template>
  <Head title="AIModels"/>

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AI models - Create</h2>
    </template>


    <!-- Step 1 modal -->
    <modal :max-width="'md'" :show="showStep1Modal" @close="showStep1Modal = false" :closeable="true">
      <template #header>
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
            id="modal-title">
          Select a dataset
        </h3>
      </template>
      <template #body>
        <div class="mt-2 flex">
          <!-- select with datasets and  labels -->
          <div class="flex-grow">

            <select id="dataset" name="dataset"
                    class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border rounded-md appearance-none focus:shadow-outline dark:bg-gray-700 dark:text-gray-200"
                    required v-on:change="updateDataset($event)">
              <option>Choose a dataset</option>
              <option v-for="dataset in user.data_sets" :key="dataset.id"
                      :value="dataset.id">
                {{ dataset.data_set_name }} - {{
                  dataset.data_set_description.length >
                  12 ? dataset.data_set_description.substring(0, 12) + '...' :
                      dataset.data_set_description
                }}
              </option>
            </select>
          </div>
          <!-- select button -->
          <div class="flex-shrink">
            <primarybutton @click="selectDataset()">Select</primarybutton>
          </div>
        </div>
      </template>
    </modal>


    <!--  Step2 modal -->
    <modal :max-width="'md'" :show="showStep2Modal" @close="showStep2Modal = false" :closeable="true">
      <template #header>
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
            id="modal-title">
          Configure training parameters
        </h3>
      </template>
      <template #body>
        <div>
          <div>
            <div class=" dark:text-gray-200 overflow-hidden sm:rounded-lg py-5">
              <!-- inputs for training params -->
              <div v-for="param in Object.keys(trainingParams)" :key="param" class="flex-grow mt-1">
                <label for="dataset" class=" pl-3 pr-6 text-base placeholder-gray-600   appearance-none">{{
                    param
                  }}</label>
                <input v-if="trainingParams[param].type == 'int'" type="number"
                       :disabled="trainingParams[param].disabled" v-model="trainingParams[param].value"
                       :max="trainingParams[param].max" :min="trainingParams[param].min"
                       class="h-10 pl-3 pr-6 text-base placeholder-gray-600 border  dark:bg-gray-700 dark:text-gray-200 rounded focus:shadow-outline"
                       required>
                <input v-else-if="trainingParams[param].type == 'float'" type="number"
                       :disabled="trainingParams[param].disabled" v-model="trainingParams[param].value"
                       class="h-10 pl-3 pr-6 text-base placeholder-gray-600 border  dark:bg-gray-700 dark:text-gray-200 rounded focus:shadow-outline"
                       required>
                <input v-else-if="trainingParams[param].type == 'string'" type="text"
                       :disabled="trainingParams[param].disabled" v-model="trainingParams[param].value"
                       class="h-10 pl-3 pr-6 text-base placeholder-gray-600 border  dark:bg-gray-700 dark:text-gray-200 rounded focus:shadow-outline"
                       required>
              </div>
            </div>

            <!-- select button -->
            <div class="flex items-end mt-5">
              <primarybutton @click="configureParams()">Configure</primarybutton>
            </div>
          </div>
        </div>
      </template>
    </modal>


    <div class="py-12">
      <!-- step 1 -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex">
              <div class="flex-grow">
                <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                  {{
                    selectedDataset == null ? "Step 1: Select a dataset" : "Step 1: " + selectedDataset.data_set_name + "(selected)"
                  }} </h3>
                <p class="text-gray-500 dark:text-gray-400">Select a dataset to use for training your model.
                </p>
              </div>
              <div class="flex-shrink">
                <primarybutton @click="selectDatasetModelModalToggle">{{
                    selectedDataset == null ? "Select" : "Change"
                  }}
                </primarybutton>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- step 2 -->
      <!-- training params -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex">
              <div class="flex-grow">
                <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Step 2:
                  Training parameters</h3>
                <p class="text-gray-500 dark:text-gray-400">{{
                    selectedDataset == null ? "Select a dataset first" : trainingParams.epochs.value + " epochs, " + trainingParams.batch_size.value + " batch size, " + trainingParams.learning_rate.value + " learning rate, " + trainingParams.base.value + " base model"
                  }}
                </p>
              </div>
              <div class="flex-shrink">
                <primarybutton @click="configureParams" v-show="selectedDataset != null">Configure</primarybutton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- step 3 -->
    <!-- model name and run it-->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5" v-show="selectedDataset != null">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <div class="flex">
            <div class="flex-grow">
              <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-1">Step 3:
                Model name</h3>
              <input
                  class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border rounded-md appearance-none focus:shadow-outline dark:bg-gray-700 dark:text-gray-200"
                  type="text" placeholder="Model name" v-model="modelName" required>

            </div>
            <div class="flex items-end">
              <primarybutton @click="trainModel" v-show="modelName != null ">
                Train
              </primarybutton>
            </div>
          </div>
        </div>
      </div>
    </div>


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
