<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import {ref} from 'vue';
import {defineProps, watch, reactive} from 'vue';
import pill from '@/Components/Pill.vue';
import primarybutton from '@/Components/PrimaryButton.vue';
import secondarybutton from '@/Components/SecondaryButton.vue';
import dangerbutton from '@/Components/DangerButton.vue';
import ai_console from '@/Components/AIConsole.vue';
import modal from '@/Components/Modal.vue';


const props = defineProps({
    user: Object,
    followed_models: Array,
    own_models: Array,
});

const reactive_user = ref(props.user);
const model_console = ref({});
const modal_open = ref(false);


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
        // fromMetricsToChart(JSON.parse(model.model_metrics));
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



var openConsole = (model) => {
    model_console.value = model
    console.log(model_console.value)
    modal_open.value = true;
}

var closeConsole = () => {
    modal_open.value = false;
}


var deleteModel = (model) => {

    // ask for confirmation
    if (!confirm('Are you sure you want to delete this model?')) {
        return;
    }


    axios.delete('/models/' + model.id)
        .then(function (response) {
            reactive_user.value = reactive_user.value.filter(x => x.id !== model.id);
        })
}


</script>

<template>
    <Head title="AIModels"/>

    <AuthenticatedLayout>
        <template #header_left>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AI models</h2>
        </template>
        <template #header_right>
            <div class="flex-shrink-0">
                <primarybutton @click="goto('/models/create')">
                    Create a model
                </primarybutton>
            </div>
        </template>


        <div class="py-12">

            <modal :show="modal_open" @close="closeConsole()" :closeable="true">
                <template #header>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Training
                        console</h2>
                </template>
                <template #body>
                    <ai_console :model="model_console"></ai_console>

                </template>
            </modal>


            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Your models</h1>
                <hr class="my-2.5">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md"
                     v-if="reactive_user.models != undefined && reactive_user.models.length > 0">
                    <div class="p-6 text-gray-900 dark:text-gray-100" v-for="model in own_models">
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
                                    <!--                                            dataset pill-->
                                    <span>
                                                <pill :left="{text: 'dataset', color: 'red-800'}"
                                                      :right="{text: model.dataset.data_set_name, color: 'red-500'}"></pill>
                                            </span>


                                    <!-- tailwind pill  -->
                                    <div v-if="model.model_hyperparameters != undefined">
                                        <pill
                                            v-for="hyperparameter in hyperParametersToArray(model.model_hyperparameters)"
                                            :left="{text: hyperparameter.name, color: 'gray-800'}"
                                            :right="{text: hyperparameter.value, color: 'gray-500'}"></pill>
                                    </div>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <secondarybutton class="mr-1" v-if="model.status == 'trained'"
                                                 @click="goto(`/models/${model.id}`)">
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md" v-else>
                    <div class="p-6 text-gray-900 dark:text-gray-100">You have no models created.
                        <a href="/models/create" class="text-blue-500 hover:text-blue-700">Create a model</a> to get
                        started.
                    </div>
                </div>
                <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-5">Followed models</h1>
                <hr class="my-2.5">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md"
                     v-if="followed_models != undefined && followed_models.length > 0">
                    <div class="p-6 text-gray-900 dark:text-gray-100" v-for="model in followed_models">
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
                                    <!--                                            author pill-->
                                    <span>
                                                <pill :left="{text: 'author', color: 'gray-800'}"
                                                      :right="{text: model.author.name, color: 'gray-500'}"></pill>
                                            </span>

                                    <!-- tailwind pill  -->
                                    <div v-if="model.model_hyperparameters != undefined">
                                        <pill
                                            v-for="hyperparameter in hyperParametersToArray(model.model_hyperparameters)"
                                            :left="{text: hyperparameter.name, color: 'gray-800'}"
                                            :right="{text: hyperparameter.value, color: 'gray-500'}"></pill>
                                    </div>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <secondarybutton class="mr-1" v-if="model.status == 'trained'"
                                                 @click="goto(`/models/${model.id}`)">
                                    Explore
                                </secondarybutton>
                                <secondarybutton class="mr-1" v-else>
                                    {{ model.status }}
                                </secondarybutton>
                                <primarybutton @click="openConsole(model)">
                                    Training console
                                </primarybutton>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- v-else -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md" v-else>
                    <div class="p-6 text-gray-900 dark:text-gray-100">You are not following any models.
                        <a href="/dashboard" class="text-blue-500 hover:text-blue-700">Explore models</a> to get
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
