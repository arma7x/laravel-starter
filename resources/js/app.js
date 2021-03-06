/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('eloquent-date-to-local', require('./components/EloquentDateToLocalComponent.vue').default); // only use with wire:ignore

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});

document.addEventListener('livewire:load', function () {
    Livewire.hook('message.sent', () => {
        console.log('Livewire@message.sent');
        _globalSetLoadingIndicator(true);
    })

    Livewire.hook('message.processed', () => {
        console.log('Livewire@message.processed');
        _globalSetLoadingIndicator(false);
    })

    Livewire.hook('message.failed', () => {
        console.log('Livewire@message.failed');
        _globalSetLoadingIndicator(false);
    })
})
