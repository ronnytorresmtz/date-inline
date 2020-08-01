<template>
    <div>
        <div class="flex items-center" @keyup.esc="closeDatePicker">
            
            <input
                :dusk="field.attribute"
                :name="field.name"
                :id="fieldId"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :value="field.value"
                maxlength="10"
                ref="datePicker"
                type="text"
                :placeholder="field.name"
                @change="dateChange"
                :disabled="field.readonly"
            />
        </div>
        <div >
            <div class="text-danger text-sm" v-show="!isValidDate">* Date is not valid</div>
        </div>
        <div v-if="field.showOverdue">
            <div class="text-danger text-sm" v-show="isOverdue">* Date is overdue</div>
        </div>
    </div>
</template>

<script>

import flatpickr from 'flatpickr'
import 'flatpickr/dist/themes/airbnb.css'
import { FormField, HandlesValidationErrors } from 'laravel-nova';

export default {
    mixins: [FormField, HandlesValidationErrors],
    
    props: ['resourceName', 'field'],

    computed: {
            resourceId() {
                return this.$parent.resource.id.value;
            },
            fieldId() {
                return `inline-text-field-${this.field.name}-${this.resourceId}`;
            },
        },
    data: () => ({ 
        flatpickr: null,
        isValidDate: true,
        isOverdue: false,
    }),
    mounted() {
        this.$nextTick(() => {
            this.flatpickr = flatpickr(this.$refs.datePicker, {
                allowInput: true,
            })
        })
        this.dateIsOverDue(this.field.value);
    },
    methods: {

        dateChange(e) {
            this.isOverdue = false;
            this.isValidDate = false;
            this.isValidDate = this.checkValidDate(e.target.value);

            this.field.value = e.target.value;

            if (this.isValidDate) {

                this.dateIsOverDue(e.target.value);

                let formData = new FormData();
                formData.append(this.field.attribute, e.target.value);
                formData.append('_method', 'PUT');
    
                return Nova.request().post(`/nova-api/${this.resourceName}/${this.resourceId}`, formData)
                    .then(
                        () => {
                            this.$toasted.show(`${this.field.name} updated to "${e.target.value}"`, {
                                type: 'success' 
                            });
                    }, 
                    (response) => {
                        this.$toasted.show(response, { 
                            type: 'error' 
                        });
                    })

            } else {

                this.$toasted.show(`${e.target.name} ${e.target.value} is not valid ('YYYY-MM-DD')`, { 
                    type: 'error' 
                });

            }
        },

        // refreshTable() {
        //     this.$parent.$parent.$parent.$parent.$parent.$parent.getResources();
        // },

        dateIsOverDue(value) {

            if (moment().format('YYYY-DD-MM') === moment(value).format('YYYY-DD-MM')) {
                return;
            }

            this.isOverdue = (value === null) ? false : moment().isAfter(moment(value));
        },

        checkValidDate(date) {
            if (date.length === 0) {
                return true;
            }
            if (date.length !== 10) {
                return false;
            }
            if (moment(date, 'YYYY-MM-DD').isValid()) {
                return true;
            }
            return false;
        },

        closeDatePicker() {
            this.flatpickr.close();
        },

        // clear() {
        //     this.flatpickr.clear()
        // },
    },

    beforeDestroy() {
        this.flatpickr.destroy()
    },
    
}
</script>

<style scoped>

.\!cursor-not-allowed {
    cursor: not-allowed !important;
}

</style>

