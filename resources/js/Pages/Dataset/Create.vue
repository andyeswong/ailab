<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
});

const reactive_user = ref(props.user);
const dataset_file = ref(null);
const datasetName = ref(null);
const datasetDescription = ref(null);


const onFileChange = (e) => {
    // max size 100mb
    if (e.target.files[0].size > 100000000) {
        alert('File too large. Max size is 100mb');
        return;
    }

    dataset_file.value = e.target.files[0];
}

const onFileDrop = (e) => {
    e.preventDefault();
    e.stopPropagation();
    // max size 100mb
    if (e.dataTransfer.files[0].size > 100000000) {
        alert('File too large. Max size is 100mb');
        return;
    }

    // file type must be csv
    if (e.dataTransfer.files[0].type != 'text/csv') {
        alert('File must be csv');
        return;
    }
    dataset_file.value = e.dataTransfer.files[0];
}

const uploadDataset = async () => {
    if (dataset_file.value == null) {
        alert('Please select a file');
        return;
    }

    if (datasetName.value == null) {
        alert('Please enter a dataset name');
        return;
    }

    if (datasetDescription.value == null) {
        alert('Please enter a dataset description');
        return;
    }

    // upload file
    const formData = new FormData();
    formData.append('file', dataset_file.value);
    formData.append('data_set_name', datasetName.value);
    formData.append('user_id', reactive_user.value.id);
    formData.append('data_set_description', datasetDescription.value);
    const response = await axios.post('/datasets', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });

    // redirect to dataset page
    window.location.href = '/datasets/';
}




</script>

<template>
    <Head title="Datasets" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Datasets - Create</h2>
        </template>



        <div class="py-12">
            <!-- step 1 -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex">
                            <div class="flex-grow" v-if="dataset_file == null">
                                <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Step 1:
                                    Upload your csv</h3>
                                <div class="flex-grow">
                                    <!-- file drop zone -->
                                    <div id="dataset_dropzone">
                                        <div class="flex justify-center items-center h-32" @drop="onFileDrop"
                                            @dragover.prevent>
                                            <div class="text-center">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                                    id="modal-title">
                                                    Drag and drop your csv file here
                                                </h3>
                                                <!-- Drag and drop file here text -->
                                                <div class="flex justify-center items-center">
                                                    <svg class="h-8 w-8 text-gray-700 dark:text-gray-200"
                                                        x-show="dataset_file == null" x-cloak
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>

                                                </div>
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                                    id="modal-title">
                                                    or
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    <button
                                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                                                        @click="$refs.fileInput.click()">
                                                        Select file
                                                    </button>
                                                    <input type="file" ref="fileInput" class="hidden" accept=".csv"
                                                        @change="onFileChange">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow" v-else>
                                <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Step 1:
                                    Upload your csv</h3>
                                <div class="flex-grow">
                                    <!-- show dataset_file file name -->
                                    <div class="flex justify-center items-center h-32">
                                        <div class="text-center">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                                id="modal-title">
                                                {{ dataset_file.name }}
                                            </h3>
                                            <!-- Drag and drop file here text -->

                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                                id="modal-title">
                                                or
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" v-if="dataset_file == null">
                                                <button
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                                                    @click="$refs.fileInput.click()">
                                                    Select file
                                                </button>
                                                <input type="file" ref="fileInput" class="hidden" accept=".csv"
                                                    @change="onFileChange">
                                            </p>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" v-else>
                                                <button
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                                                    @click="$refs.fileInput.click()">
                                                    Change file
                                                </button>
                                                <input type="file" ref="fileInput" class="hidden" accept=".csv"
                                                    @change="onFileChange">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- step 2 -->
        <!-- dataset name and run it-->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5"  v-if="dataset_file != null">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex">
                        <div class="flex-grow">
                            <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-1">Step 2:
                                Dataset name and description</h3>
                            <input
                                class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border rounded-lg appearance-none focus:shadow-outline dark:bg-gray-700 dark:text-gray-200"
                                type="text" placeholder="Dataset name" v-model="datasetName" required>

                        </div>
                    </div>
                    <!-- textarea for description -->
                    <div class="flex mt-3">
                        <div class="flex-grow">
                            <textarea lines="3"
                                class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border rounded-lg appearance-none focus:shadow-outline dark:bg-gray-700 dark:text-gray-200"
                                type="text" placeholder="Dataset description" v-model="datasetDescription" required>
                            </textarea>
                        </div>
                    </div>
                    <!-- button -->
                    <div class="flex mt-3">
                        <div class="flex-grow">
                            <button
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                                @click="uploadDataset">
                                Upload dataset
                            </button>
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
