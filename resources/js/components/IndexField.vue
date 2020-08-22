<template>
    <div>
        <div class="flex items-center" @keyup.esc="closeDatePicker">
            <input
                v-if="field.inlineOnIndex && isEditable"
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
                @blur="closeEdit"
                :disabled="field.readonly"
            />
            <span v-else class="whitespace-no-wrap cursor-pointer" @click="openCalendar">{{ field.value }}</span>
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

    created() {
        document.addEventListener('keyup', (evt) => {
            if (evt.keyCode === 27) {
                this.closeEdit();
            }
        });
    },

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
        isEditable: false,
    }),

    
    mounted() {
        this.field.value = this.field.value || '—';
        this.dateIsOverDue(this.field.value);
    },
    methods: {

        closeEdit() {
            this.isEditable = false;
            this.field.value = this.field.value || '—';
        },
        openCalendar() {
            this.isEditable=true;
            this.field.value = (this.field.value === '—') ? '' : this.field.value;
            this.$nextTick(() => {
                this.flatpickr = flatpickr(this.$refs.datePicker, {
                    allowInput: true,
                });
            });
        },

        dateChange(e) {

            this.isEditable = false;
            this.isOverdue = false;
            this.isValidDate = this.checkValidDate(e.target.value);

            this.field.value = e.target.value;

            if (!this.field.value) {
                this.field.value = '—';
            }

            if (this.isValidDate) {
                
                this.dateIsOverDue(e.target.value);

                if (!this.checkGreaterThan(e)) {
                    this.$toasted.show('Start Date is greater than End Date', { 
                        type: 'error' 
                    });
                }

                let formData = new FormData();
                formData.append(this.field.attribute, e.target.value);
                formData.append('_method', 'PUT');
    
                return Nova.request().post(`/nova-api/${this.resourceName}/${this.resourceId}`, formData)
                    .then(
                        () => {
                            this.$toasted.show(`${this.field.name} updated to "${e.target.value || '—'}"`, {
                                type: 'success' 
                            });
                    }); 
               
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

            if (moment().format('YYYY-DD-MM') === moment(value,'YYYY-DD-MM').format('YYYY-DD-MM')) {
                return;
            }

            this.isOverdue = (value === null) ? false : moment().isAfter(moment(value, 'YYYY-DD-MM'));
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

        checkGreaterThan(e) {
            
            const currentFieldValue = e.target.value;
            const currentFieldName = this.field.name;
            const isTheGreaterField = this.field.hasOwnProperty('greaterThan');

            if (isTheGreaterField) {
                const dateValue = this.$parent.resource.fields.filter((field) => {
                    if (field.name === this.field.greaterThan) {
                        return field;
                    }
                });
                if (['', '—'].includes(currentFieldValue) || ['', '—'].includes(dateValue[0].value)){
                    return true;
                }
                if (currentFieldValue === dateValue[0].value) {
                    return true;
                }
                return moment(currentFieldValue).isAfter(moment(dateValue[0].value));
            } else {
                const dateValue = this.$parent.resource.fields.filter((field) => {
                    if (field.hasOwnProperty('greaterThan')) {
                        return field;
                    }
                });
                if (['', '—'].includes(currentFieldValue) || ['', '—'].includes(dateValue[0].value)){
                    return true;
                }
                if (currentFieldValue === dateValue[0].value) {
                    return true;
                }
                return moment(currentFieldValue).isBefore(moment(dateValue[0].value));
            }
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

