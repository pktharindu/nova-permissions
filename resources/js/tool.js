import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((Vue, router) => {
    Vue.component('index-field-checkboxes', IndexField)
    Vue.component('detail-field-checkboxes', DetailField)
    Vue.component('form-field-checkboxes', FormField)
})
