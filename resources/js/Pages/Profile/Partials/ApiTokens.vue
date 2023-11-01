<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import modal from '@/Components/Modal.vue';

import {ref, defineProps, computed} from 'vue';

const props = defineProps({
    api_tokens: Array,
    models: Array,
    followed_models: Array,
});

const modal_open = ref(false);
const selected_models = ref([]);
const api_token_name = ref('');

const createApiToken = () => {
    modal_open.value = true;
}

const selectModel = (model) => {
    if (selected_models.value.includes(model)) {
        selected_models.value = selected_models.value.filter(item => item !== model);
    } else {
        selected_models.value.push(model);
    }
}

const saveApiToken = () => {
    var model_tokens = [];
    selected_models.value.forEach(model => {
        model_tokens.push(model.model_token);
    });

    var form_data = {
        name: api_token_name.value,
        model_tokens: model_tokens,
    }

    var url = '/api/v1/api-tokens';

    axios
        .post(url, form_data)
        .then(function (response) {
            console.log(response);
            modal_open.value = false;
        })
        .catch(function (error) {
            console.log(error);
        });

    console.log(model_tokens);
    modal_open.value = false;
}

const showToken = (token) => {
    token.show = true;
}


//computed
const prepared_tokens = computed(() => {
    return props.api_tokens.map((token) => {
    //     if token does not have show property add it with false value
        if (!token.hasOwnProperty('show')) {
            token.show = false;
        }
        return token;
    });
});


</script>

<template>
    <modal :show="modal_open" :closeable="true" @close="modal_open = false">
        <template #header>
            <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Generate API Token
            </h3>
        </template>

        <template #body>
            <div class="space-y-6">
                <!--                name input-->
                <div>
                    <InputLabel for="name" value="Name"/>
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="api_token_name"
                        autofocus
                        autocomplete="name"
                    />
                    <InputError message="This field is required."/>

                </div>
                <p>
                    Select the models that this token will be able to access.
                </p>
                <div v-if="models.length >0">
                    <span class="text-gray-500 mb-3">Owned models</span>
                    <div class="flex mb-1" v-for="model in models">
                        <div class="flex-grow">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                v-text="model.model_name"></h3>
                        </div>
                        <div class="flex-shrink">
                            <input class="rounded-md" type="checkbox" @click="selectModel(model)">
                        </div>
                    </div>
                </div>
                <div v-if="followed_models.length > 0 ">
                    <span class="text-gray-500 mb-3">Followed models</span>
                    <div class="flex mb-1" v-for="model in followed_models">
                        <div class="flex-grow">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight"
                                v-text="model.model_name"></h3>
                        </div>
                        <div class="flex-shrink">
                            <input class="rounded-md" type="checkbox" @click="selectModel(model)">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6">
                <PrimaryButton @click="saveApiToken">
                    Generate API Token
                </PrimaryButton>
            </div>
        </template>

    </modal>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Api Tokens</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your API tokens.
            </p>

            <div class="mt-6 w-full">
                <table class="w-full" v-if="api_tokens.length > 0">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                            Expiration
                        </th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700"></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="token in prepared_tokens" :key="token.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ token.name }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ token.last_used_at ? 'Active' : 'Never' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button v-if="!token.show"
                                    @click="showToken(token)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                Show
                            </button>
                            <span v-else class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                {{ token.token }}
                            </span>
                        </td>
                    </tr>
                    </tbody>

                </table>

                <div v-else>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        You have not created any API tokens.
                    </p>
                </div>

            </div>

            <div class="mt-6">
                <PrimaryButton @click="createApiToken">
                    Generate API Token
                </PrimaryButton>
            </div>


        </header>


    </section>
</template>
