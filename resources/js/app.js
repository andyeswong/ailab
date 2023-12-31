import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';




// define global methods
const hyperParametersToArray = (json_string) => {
//      {"epochs":5,"batch_size":5,"learning_rate":0.0005}
    let hyperparameters = JSON.parse(json_string);
    let hyperparameters_array = [];
    for (let [key, value] of Object.entries(hyperparameters)) {
        var obj = {name : key, value : value};
        hyperparameters_array.push(obj);
    }
    return hyperparameters_array;
}

const goto = (route) => {
    window.location.href = route;
}

const goback = () => {
    window.history.back();
}

var modelMetricsLastObject = (model_metrics) => {
    var metrics = JSON.parse(model_metrics);
    if (metrics == undefined) {
        return {};
    } else if (metrics.length == 0) {
        return {};
    }
    var last_object = metrics[metrics.length - 1];
    return metrics[metrics.length - 1];
}


// create global methods array
const methods = {
    goto: goto,
    goback: goback,
    hyperParametersToArray: hyperParametersToArray,
    modelMetricsLastObject: modelMetricsLastObject,
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mixin({ methods: methods })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
