Nova.booting((Vue, router) => {
    Vue.component('index-FieldCheckboxes', require('./components/IndexField'));
    Vue.component('detail-FieldCheckboxes', require('./components/DetailField'));
    Vue.component('form-FieldCheckboxes', require('./components/FormField'));

})
