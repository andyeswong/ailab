<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import {reactive} from 'vue';
import {ref} from 'vue';
import secondarybutton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import csvEditor from "@/Components/CsvEditor.vue";

const props = defineProps({
    user: Object,
    dataset: Object,
});

const socket = io(window.api_host, {
    transports: ['websocket'],
    upgrade: true,
    reconnection: true,
    reconnectionDelay: 1000,
    reconnectionDelayMax: 5000,
    reconnectionAttempts: Infinity
});

const csv_data = ref('');

const loading_data = ref(false);


var host = window.location.hostname;
var channel_string = host + '_' + props.user.id.toString();

// when the file itself is uploaded, then the socket will emit the file name and path
socket.on(channel_string+'_file_download', (data) => {
    csv_data.value = data.file_content;
});

socket.on(channel_string+'_file_update', (data) => {
    window.location.reload();
});


//get csv file from engine through socket
const requestFile = () =>{
    var path = props.dataset.data_set_path;
    var file_obj = {
        file_path: path,
        channel : channel_string
    }
    socket.emit('file_download', file_obj);
}

const updateFile = (content) => {
    console.log(content);
    loading_data.value = true;
    var path = props.dataset.data_set_path;
    var file_obj = {
        file_path: path,
        channel : channel_string,
        file_content: content
    }
    socket.emit('file_update', file_obj);
}


setTimeout(() => {
    requestFile();
}, 1000);




</script>

<template>
    <Head title="AIModels"/>

    <AuthenticatedLayout>
        <template #header_left>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Datasets - Explore {{
                    dataset.data_set_name
                }}</h2>
        </template>

        <div class="py-12">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
                    <span class="text-gray-600 dark:text-gray-400">
        Click on a row to edit it or remove it, or click on the button below to add a new row
    </span>
                <br>

                <span class="text-gray-600 dark:text-gray-400">
        Press ctrl + s to save changes
    </span>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8  mt-3">
                <div v-if="!data_loading" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-md p-5">
                    <csv-editor :csvcontent="csv_data" v-on:update:csvcontent="updateFile"></csv-editor>
                </div>
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-md p-5">
                    <div class="flex flex-col">
                        <div class="flex flex-grow">
                            <div class="flex-grow">
                                <p class="text-gray-600 dark:text-gray-400">
                                    Loading data...
                                </p>
                            </div>
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

#left_container{
    transition: all 0.25s ease;
}

.coding {
    font-family: 'Fira Code', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    tab-size: 4;
    white-space: pre-wrap;
    word-break: break-all;
}
</style>
