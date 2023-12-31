<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import {reactive} from 'vue';
import {ref} from 'vue';
import chat from '@/Components/ChatBox.vue';
import ai_console from '@/Components/AIConsole.vue';
import secondarybutton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import CsvEditor from "@/Components/CsvEditor.vue";

const props = defineProps({
    user: Object,
    model: Object,
    is_author: Boolean,
    is_followed: Boolean,
    dataset: Object,
    token: String,
});

// const chat = ref('');

const search_word = ref('');

const _model = reactive(props.model);
const expanded_chat = ref(false);
const integration_code_modal = ref(false);

const expandChat = () => {
    expanded_chat.value = true;
}

const model_integration_code = ref('');
model_integration_code.value = 'const axios = require(\'axios\');\n' +
    'const FormData = require(\'form-data\');\n\n' +
    'let data = new FormData();\n\n' +
    'data.append(\'api_token\', \'{api_token}\');\n\n' +

    'data.append(\'prompt\', \'hi, show me reports\');\n\n' +
    'data.append(\'temperature\', \'0.1\');\n' +
    'data.append(\'max_tokens\', \'256\');\n' +
    '\n' +
    'let config = {\n' +
    '  method: \'post\',\n' +
    '  maxBodyLength: Infinity,\n' +
    '  url: \''+window.location.protocol+'//'+window.location.host+'/api/v1/integrations/prompt/' + _model.model_token + '\',\n' +
    '  data : data\n' +
    '};\n' +
    '\n' +
    'axios.request(config)\n' +
    '.then((response) => {\n' +
    '  console.log(JSON.stringify(response.data));\n' +
    '})\n' +
    '.catch((error) => {\n' +
    '  console.log(error);\n' +
    '});\n'





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
    var file_content = data.file_content;

    csv_data.value = file_content;
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

if(props.is_author){
    setTimeout(() => {
        requestFile();
    }, 1000);
}


const search = (prompt) => {
    search_word.value = prompt;
    console.log(search_word.value);
}





</script>

<template>
    <Head title="AIModels"/>

    <AuthenticatedLayout>
        <template #header_left>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AI models - Explore {{
                    model.model_name
                }}</h2>
        </template>

        <modal :show="integration_code_modal" :closeable="true" @close="integration_code_modal = false">
            <template #header>
                <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Integration code
                </h3>
            </template>
            <template #body>
                <div class="flex flex-col">
                    <div class="flex flex-grow">
                        <div class="flex-grow">
                            <p class="text-gray-600 dark:text-gray-400">
                                Copy and paste the following code into your project to integrate this model.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-grow">
                        <div class="flex-grow">
                            <textarea class="w-full h-64 rounded-md coding inverse-toggle p-5 text-gray-100 text-sm font-mono subpixel-antialiased
              bg-gray-800  pb-6 leading-normal overflow-auto max-h-56 " v-model="model_integration_code" readonly>
                            </textarea>
                        </div>
                    </div>

                    <span class="text-gray-600 dark:text-gray-400">
                        Replace {api_token} with your api token. get your api token <a href="/profile"><strong>here</strong></a>
                    </span>
                </div>
            </template>
        </modal>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex mb-5">
                <secondarybutton v-if="!expanded_chat" @click="expandChat">Expand chat</secondarybutton>
                <secondarybutton v-else @click="expanded_chat = false">Collapse chat</secondarybutton>

                <secondarybutton class="ml-1" v-if="integration_code_modal" @click="integration_code_modal = false">Hide {}</secondarybutton>
                <secondarybutton class="ml-1" v-else @click="integration_code_modal = true">Show {}</secondarybutton>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
<!--                left modules-->
                    <div id="left_container"  :class="expanded_chat ? 'chat_container flex-grow max-w-3xl':'chat_container flex-shrink max-w-sm'">
                        <chat :model="model" :auth_token="token" @search_prompt="search"></chat>
                        <hr class="my-2.5">
                        <ai_console :model="model"></ai_console>
                    </div>


<!--                rigth modules-->
                <div id="right_container" :class="'flex-grow'">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md flex-grow mx-5">
                        <div id="model_info_container" class="p-6 text-gray-900 dark:text-gray-100 h-full">
                            <div class="flex">
                                <div class="flex-grow">
                                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                        v-text="model.model_name"></h3>
                                </div>
                                <div class="flex-shrink" v-if="is_author">
                                    <secondarybutton v-if="model.is_public == 1"
                                                     @click="goto('/models/'+model.id+'/make/private')">Make model private
                                    </secondarybutton>
                                    <secondarybutton v-else @click="goto('/models/'+model.id+'/make/public')">Make model
                                        public
                                    </secondarybutton>

                                    <!--                                retrain button-->
                                    <secondarybutton class="ml-1" @click="goto('/models/'+model.id+'/retrain')">Retrain
                                    </secondarybutton>
                                </div>
                                <div v-else class="flex-shrink">
                                    <!--                                follow button-->
                                    <secondarybutton v-if="is_followed"
                                                     @click="goto('/models/'+model.id+'/unfollow')">Unfollow
                                    </secondarybutton>
                                    <secondarybutton v-else @click="goto('/models/'+model.id+'/follow')">Follow
                                    </secondarybutton>

                                    <!--                                author button-->
                                    <secondarybutton class="ml-1" @click="goto('/users/'+model.user.id+'/show')">Author
                                    </secondarybutton>
                                </div>
                            </div>


                            <hr class="my-2.5">
                            <div class="flex flex-col">
                                <div class="flex flex-grow">
                                    <div v-html="model.model_description"></div>
                                </div>

                                <hr class="my-2.5">
                            </div>
                        </div>
                    </div>

<!--                    comments-->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md flex-grow m-5">
                        <div id="model_comments_container" class="p-6 text-gray-900 dark:text-gray-100 h-full">
                            <div class="flex">
                                <div class="flex-grow">
                                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                       >comments</h3> (👷under dev)
                                </div>
                            </div>
                        </div>
                    </div>

<!--                    dataset-->
                    <div  v-if="is_author" class=" max-w-4xl bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md flex-grow m-5">
                        <div id="model_dataset_container" class="p-6 text-gray-900 dark:text-gray-100 ">
                            <div class="flex mb-3">
                                <div class="flex-grow">
                                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                    >Dataset</h3>
                                </div>
                                <div class="flex-shrink">
                                    <secondarybutton @click="goto('/datasets/'+dataset.id)">Edit dataset</secondarybutton>
                                </div>
                            </div>

                            <div class="flex  max-h-96 overflow-y-auto">

                                <div class="flex-grow">
                                    <csv-editor :edit="false" :csvcontent="csv_data" v-on:update:csvcontent="updateFile" :search_word="search_word"></csv-editor>
                                </div>
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
