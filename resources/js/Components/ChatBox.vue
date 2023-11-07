<script setup>
import {defineProps, ref, reactive, defineEmits} from 'vue';
import primarybutton from '@/Components/PrimaryButton.vue';

// emitter for search prompt
const emit = defineEmits(['search_prompt']);


const props = defineProps({
    model: Object,
    auth_token: String,
});

const messages = ref(
    []
);

const chat_temperature = ref(0.5);
const chat_max_chars = ref(100);

const chat_message = ref('');
const loading_chat = ref(false);


// add listener for enter key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        // if chat-textarea is focused
        if (document.activeElement.id === 'chat-textarea') {
            event.preventDefault();
            sendChat();
        }
    }
});

const sendChat = () => {
    // create message object
    var msg_obj = {
        prompt: chat_message.value,
        completion: null,
    };

    loading_chat.value = true;

    var form_data = new FormData();
    // add breakline to chat
    form_data.append('prompt', chat_message.value);
    form_data.append('model_token', props.model.model_token);
    form_data.append('temperature', chat_temperature.value);
    form_data.append('max_tokens', chat_max_chars.value);

    axios.post('/api/v1/completions/' + props.model.model_token, form_data, {
        headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': 'Bearer ' + props.auth_token,
        }
    })
        .then(function (response) {
            console.log(response);
            const completion = response.data.completion;
            // add completion to chat
            msg_obj.completion = completion;
            // add message to chat box
            messages.value.push(msg_obj);
            chat_message.value = '';
            loading_chat.value = false;

        })
        .catch(function (error) {
            console.log(error);
        });

    var messages_container = document.getElementById('messages_container');
    messages_container.scrollTop = messages_container.scrollHeight;

}

const reAsk = (prompt) => {
    chat_message.value = prompt;
}


</script>

<template>
    <div id="chat_container">
        <div id="messages_container"
             class="grid grid-1 rounded-t-md bg-gray-200 p-5 max-h-56 overflow-y-auto dark:bg-gray-800">
            <div v-if="messages.length > 0" v-for="message in messages">


                <div class="text-start mb-5">
                    <div v-if="message.prompt != null" class="bg-gray-700 p-3 rounded-t-md rounded-br-md ">
                        <span class="text-white">{{ message.prompt }}</span>
                    </div>
                    <span class="text-gray-500">you</span> -
                    <button
                        class="text-gray-800 hover:text-gray-400 dark:text-white" @click="reAsk(message.prompt)">re-ask
                    </button>
                    -
                    <button class="text-gray-800 hover:text-gray-400 dark:text-white"
                            @click="emit('search_prompt', message.prompt)">search
                    </button>
                </div>


                <div class="text-end">
                    <div v-if="message.completion != null"
                         class="bg-white dark:bg-gray-200 p-3 rounded-t-md rounded-bl-md text-end">
                        <span class="text-gray-800">{{ message.completion }}</span>
                    </div>
                    <span class="text-gray-500">{{ model.model_name }}</span>
                </div>

            </div>
            <div v-else>
                <div class="text-start mb-5">
                    <div class="bg-gray-700 p-3 rounded-t-md rounded-br-md">
                        <span class="text-white">Waiting for prompt...</span>
                    </div>
                    <span class="text-gray-500">you</span>
                </div>


                <div class="text-end">
                    <div class="bg-white dark:bg-gray-200 p-3 rounded-t-md rounded-bl-md text-end">
                        <span class="text-gray-800">...</span>
                    </div>
                    <span class="text-gray-500">{{ model.model_name }}</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 rounded-b-md bg-white dark:bg-gray-700 p-5">
            <span class="text-gray-600">{{ model.model_name }} params:</span>
            <div class="grid grid-cols-2 mt-1">
                <!--        sliders for temperature and max chars-->
                <div class="p-1">
                    <input v-model="chat_temperature" class="w-full" type="range" id="temperature" name="temperature"
                           min="0" max="1" step="0.1" placeholder="temperature">
                    <span class="text-gray-600">Temperature: {{ chat_temperature }}</span>
                </div>
                <div class="p-1">
                    <input v-model="chat_max_chars" class="w-full" type="range" id="max_chars" name="max_chars" min="0"
                           max="1000" step="1" placeholder="max chars">
                    <span class="text-gray-600">Max tokens: {{ chat_max_chars }}</span>
                </div>
            </div>
            <hr class="my-2">
            <span class="text-gray-600 mb-1">Prompt:</span>
            <textarea id="chat-textarea" class="rounded-md" :disabled="loading_chat" v-model="chat_message"
                      placeholder="write you prompt here..."></textarea>
            <primarybutton :class="loading_chat ? 'mt-1 animate-pulse' : 'mt-1'" @click="sendChat"
                           :disabled="loading_chat">
                {{ loading_chat ? 'Loading' : 'Send' }}
            </primarybutton>

        </div>
    </div>


</template>

<style scoped>

</style>
