<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import { computed } from 'vue';
import { defineProps, watch, reactive } from 'vue';




const props = defineProps({
    user: Object,
});

const reactive_user = ref(props.user);


watch(reactive_user, async (newValue, oldValue) => {
    console.log('reactive_user updated');
    if (model_console != undefined) {
        var model = reactive_user.value.models.find(x => x.id === model_console.value.id);
        model_console.value = model;
    }
});




</script>

<template>
    <Head title="Datasets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <div class="flex-grow">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Datasets</h2>
                </div>

                <!-- button to create Models -->
                <div class="flex-shrink-0">
                    <a href="/datasets/create"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">
                        Upload dataset
                    </a>
                </div>
            </div>

        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                    v-if="reactive_user.datasets != undefined">
                    <div class="p-6 text-gray-900 dark:text-gray-100" v-for="dataset in reactive_user.datasets">
                        <div class="flex">
                            <div class="flex-grow">
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                    v-text="dataset.data_set_name"></h3>
                                <p class="text-gray-600 dark:text-gray-400" v-text="dataset.data_set_description"></p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    
                                </p>
                            </div>
                            <div class="flex-shrink-0">    
                                <button
                                    class=" ml-1 inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-green-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:borderblue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150"
                                    >
                                    Explore
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- v-else -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" v-else>
                    <div class="p-6 text-gray-900 dark:text-gray-100">You have no datasets uploaded.
                        <a href="/datasets/create" class="text-blue-500 hover:text-blue-700">Upload  dataset</a> to get
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
