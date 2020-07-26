Nova.booting((Vue, router, store) => {
  Vue.component('index-date-inline', require('./components/IndexField'))
  Vue.component('detail-date-inline', require('./components/DetailField'))
  Vue.component('form-date-inline', require('./components/FormField'))
})
