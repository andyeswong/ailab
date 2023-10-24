<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';


defineProps({
    user: Object,
  });
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-md" v-if="user.models.length > 0">
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
                                <a href="/models"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">
                                    Go to models
                                </a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
