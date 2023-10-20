<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    left:{
        text: String,
        color: String,
    },
    right:{
        text: String,
        color: String,
    },
})

</script>

<template>
        <span :class="'py-1 px-2 rounded-l-md text-xs font-bold text-white bg-'+left.color">{{left.text}}</span>
        <span :class="'py-1 px-2 mr-1 rounded-r-md text-xs font-bold text-white bg-'+right.color">{{right.text}}</span>
</template>

<style scoped>

</style>
