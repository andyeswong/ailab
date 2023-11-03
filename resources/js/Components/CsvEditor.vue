<script setup>
import {defineProps, ref, reactive, watch, defineEmits} from 'vue';
import draggable from 'vuedraggable'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    csvcontent: String,
    edit: Boolean,
});


const json_data = ref({});
const csv_data = ref(props.csvcontent);
const data_loaded = ref(false);
const edit_modal_open = ref(false);
const row_to_edit = ref({
    prompt: '',
    completion: '',
    col: 0,
});

const emit = defineEmits(['update:csvcontent']);


const parseCSV = () => {
    var csv = csv_data.value;
    var lines = csv.split("\n");
    var result = [];
    var headers = ['prompt', 'completion'];
    for (var i = 0; i < lines.length; i++) {
        var obj = {};
        // count commas in line
        var commas = lines[i].split(',').length - 1;
        // if there are just 1 comma, slpit by comma
        if (commas === 1){
            var currentline = lines[i].split(",");
        }else if (commas > 1){
            // if there are more than 1 comma, split by first comma
            var currentline = lines[i].split(/,(.+)/);
        }

        var prompt = currentline[0];
        var completion = currentline[1];

        if(prompt == ""){
            var currentline = lines[i].split(',"');
        }

        for (var j = 0; j < headers.length; j++) {
            obj[headers[j]] = currentline[j];
        }


        obj['col'] = i;
        result.push(obj);
    }
    // remove first element
    result.shift();


    json_data.value = result;
    data_loaded.value = true;
}

watch(csv_data, async (newVal, oldVal) => {
    parseCSV();
});

watch(() => props.csvcontent, async (newVal, oldVal) => {
    csv_data.value = newVal;
});

const closeEditModal = () => {
    edit_modal_open.value = false;
}

const editRow = (col) => {
    if(!props.edit){
        return;
    }
    var row = json_data.value.find(item => item.col === col);
    row_to_edit.value = row;
    edit_modal_open.value = true;
}

const applyChanges  = () => {
    var row = json_data.value.find(item => item.col === row_to_edit.value.col);
    row.prompt = row_to_edit.value.prompt;
    row.completion = row_to_edit.value.completion;
    edit_modal_open.value = false;
}

const addRow = () => {

    var highest_col = 0;
    json_data.value.forEach(row => {
        if(row.col > highest_col){
            highest_col = row.col;
        }
    });

    var row = {
        prompt: '',
        completion: '',
        col: highest_col + 1 ,
    }
    json_data.value.push(row);
    editRow(row.col);
}

const removeRow = (col) => {
    var row = json_data.value.find(item => item.col === col);
    var index = json_data.value.indexOf(row);
    json_data.value.splice(index, 1);
    closeEditModal();
}


const saveChanges = () => {
    var csv = 'prompt,completion\n';
    json_data.value.forEach(row => {
        csv += row.prompt + ',' + row.completion + '\n';
    });
    csv_data.value = csv;
    props.csvcontent = csv;

//     emit('update:csvcontent', csv);
    emit('update:csvcontent', csv_data.value);

}

if(props.edit){

// listen for ctrl + s
    window.addEventListener("keydown", function (event) {
        if (event.ctrlKey && event.key === 's') {
            event.preventDefault();
            saveChanges();
        }
    });

// listen for ctrl + a
    window.addEventListener("keydown", function (event) {
        if (event.ctrlKey && event.key === 'a') {
            event.preventDefault();
            addRow();
        }
    });

// listen for ctrl
    window.addEventListener("keydown", function (event) {
        event.preventDefault();
        infoModal.value = true;
    });

// listen for ctrl
    window.addEventListener("keyup", function (event) {
        event.preventDefault();
        infoModal.value = false;
    });


}

const infoModal = ref(false);


</script>

<template>

    <modal :show="infoModal" @close="infoModal = false">

        <template #body>
            <p class="text-gray-400">
                <strong> S</strong> to save changes
            </p>
            <p class="text-gray-400"   >
                <strong> A</strong> to add row
            </p>

        </template>
    </modal>

    <modal :show="edit_modal_open" @close="closeEditModal">
        <template #header>
            <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit row {{ row_to_edit.col }}
            </h3>
        </template>

        <template #body>
            <input type="text" v-model="row_to_edit.prompt" class="w-full rounded-md">
            <input type="text" v-model="row_to_edit.completion" class="w-full rounded-md mt-1">

            <primary-button class="mt-6" @click="applyChanges">Apply</primary-button>
            <secondary-button class="ml-1" @click="removeRow(row_to_edit.col)">Remove</secondary-button>


        </template>

    </modal>



    <div class="flex items-center">
        <div class="flex-shrink "  >
            <strong class="px-2 py-1 mr-1 bg-gray-800 rounded-md text-white h-full">#</strong>
        </div>
        <div class="flex-grow">
            <div class="grid grid-cols-2">
                <div class="  my-1 p-2 overflow-x-auto" >Prompt</div>
                <div class="  my-1 p-2 overflow-x-auto">Completion</div>
            </div>
        </div>


    </div>


    <draggable
        v-if="data_loaded"
        v-model="json_data"
        item-key="col"
    >
        <template #item="{element}">
            <div class="flex items-center">
                <div class="flex-shrink">
                    <strong class="px-2 py-1 mr-1 bg-gray-800 rounded-md text-white h-full">{{ element.col }}</strong>
                </div>
                <div class="flex-grow hover:scale-x-95 transition-transform"  @click="editRow(element.col)">
                    <div class="grid grid-cols-2">
                        <div class="bg-gray-200 rounded-l-md my-1 p-2 overflow-x-auto" >{{element.prompt}}</div>
                        <div class="bg-white rounded-r-md my-1 p-2 overflow-x-auto">{{element.completion}}</div>
                    </div>
                </div>
            </div>
        </template>
    </draggable>

    <div v-else>
        Loading...
    </div>

    <div v-if="edit" class="flex justify-center mt-6">
        <secondary-button @click="addRow">Add row</secondary-button>
    </div>
</template>

<style scoped>

</style>
