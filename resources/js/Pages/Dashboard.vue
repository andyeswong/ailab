<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import primarybutton from '@/Components/PrimaryButton.vue';
import pill from '@/Components/Pill.vue';



defineProps({
    user: Object,
    public_models: Array,
  });
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12 ">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<!--                    grid-->
                    <div class="grid sm:grid-cols-1 md:grid-cols-2">
<!--                        models cards-->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md md:mr-2.5" v-if="user.models.length > 0">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="flex">
                                    <div class="flex-grow" >
                                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">Models</h3>
                                        <p class="text-gray-600 dark:text-gray-400" >
                                            You have {{ user.models.length }} models created.
                                        </p>
                                    </div>
                                    <div class="flex-shrink">
                                        <!-- button to go -->
                                        <primarybutton @click="goto('/models')">
                                            View
                                        </primarybutton>
                                        or
                                        <primarybutton @click="goto('/models/create')">
                                            Create
                                        </primarybutton>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md md:mr-2.5">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="flex">
                                    <div class="flex-grow" >
                                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">Models</h3>
                                        <p class="text-gray-600 dark:text-gray-400" >
                                            You have no models created.
                                        </p>
                                    </div>
                                    <div class="flex-shrink">
                                        <!-- button to go -->
                                        <primarybutton @click="goto('/models/create')">
                                            Create
                                        </primarybutton>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="my-2.5 sm:flex md:hidden"/>
<!--                        dataset cards-->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md md:ml-2.5" v-if="user.models.length > 0">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="flex">
                                    <div class="flex-grow" >
                                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">Datasets</h3>
                                        <p class="text-gray-600 dark:text-gray-400" >
                                            You have {{ user.data_sets.length }} models created.
                                        </p>
                                    </div>
                                    <div class="flex-shrink">
                                        <!-- button to go -->
                                        <primarybutton @click="goto('/datasets')">
                                            View
                                        </primarybutton>
                                        or
                                        <primarybutton @click="goto('/datasets/create')">
                                            Create
                                        </primarybutton>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md md:ml-2.5">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="flex">
                                    <div class="flex-grow" >
                                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">Datasets</h3>
                                        <p class="text-gray-600 dark:text-gray-400" >
                                            You have no datasets created.
                                        </p>
                                    </div>
                                    <div class="flex-shrink">
                                        <!-- button to go -->
                                        <primarybutton @click="goto('/datasets/create')">
                                            Create
                                        </primarybutton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--                    public models grid-->
                    <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-5">Public models</h1>
                    <hr class="my-2.5">

                    <div class="grid grid-cols-1 ">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md py-5" v-if="public_models.length > 0">
                            <div v-for="model in public_models" class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="flex">
                                    <div class="flex-grow" >
                                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight" v-text="model.model_name"></h3>
                                        <p class="text-gray-600 dark:text-gray-400" v-text="model.model_description"></p>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            <!-- tailwind pill  -->
                                            <span v-if="modelMetricsLastObject(model.model_metrics).train_loss != undefined">
                                                <pill :left="{text: 'train_loss', color: 'gray-800'}"
                                                      :right="{text: modelMetricsLastObject(model.model_metrics).train_loss, color: 'gray-500'}"></pill>
                                            </span>

<!--                                            author pill-->
                                            <span>
                                                <pill :left="{text: 'author', color: 'blue-500'}"
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
                                    <div class="flex-shrink">
                                        <!-- button to go -->
                                        <primarybutton @click="goto(`/models/${model.id}`)">
                                            Explore
                                        </primarybutton>
                                    </div>
                                </div>
                            </div>

                        </div>
<!--                        else there is no models published-->
                        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md py-5">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="flex">
                                    <div class="flex-grow" >
                                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">Public models</h3>
                                        <p class="text-gray-600 dark:text-gray-400" >
                                            There are no public models yet.
                                        </p>
                                    </div>
                                    <div class="flex-shrink">
                                        <!-- button to go -->
                                        <primarybutton @click="goto('/models/create')">
                                            Create
                                        </primarybutton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>
