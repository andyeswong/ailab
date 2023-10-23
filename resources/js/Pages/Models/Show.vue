<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive } from 'vue';
import { ref } from 'vue';
import chat from '@/Components/ChatBox.vue';

const props = defineProps({
    user: Object,
    model: Object,
});

// const chat = ref('');

const _model = reactive(props.model);

// add listener for enter key
// document.addEventListener('keydown', function (event) {
//     if (event.key === 'Enter') {
//         // if chat-textarea is focused
//         if (document.activeElement.id === 'chat-textarea'){
//             event.preventDefault();
//             sendChat();
//         }
//     }
// });

// const sendChat = () => {
//     var form_data = new FormData();
//     // add breakline to chat
//     chat.value += '\n';
//     form_data.append('prompt', chat.value);
//     form_data.append('model_token', _model.model_token);
//
//     axios.post('http://localhost:5000/api/v1/prompt', form_data, {
//             headers: {
//                 'Content-Type': 'multipart/form-data'
//             }
//         })
//         .then(function (response) {
//             console.log(response);
//             const completion = response.data.completion;
//             // add completion to chat
//             chat.value += _model.model_name + ": " +completion;
//             chat.value += '\n';
//
//         })
//         .catch(function (error) {
//             console.log(error);
//         });
// }






</script>

<template>
    <Head title="AIModels" />

    <AuthenticatedLayout>
        <template #header_left>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AI models - Explore {{
                model.model_name }}</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-3">


              <chat :model="model"></chat>


                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex">
                            <!-- big text area  -->
                            <div class="flex-grow">
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                    v-text="model.model_name"></h3>
                            </div>
                            <div class="flex-shrink">
                                <p class="text-gray-600 dark:text-gray-400" v-text="model.model_description"></p>
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
</style>
