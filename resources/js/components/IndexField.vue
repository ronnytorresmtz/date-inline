<template>
    <div class="flex items-center" @keyup.esc="closeDatePicker">
        <div class="text-danger mr-1" v-show="!isValidDate">*</div>
        <input
            :dusk="field.attribute"
            :name="field.name"
            :id="field.namd+field.value+field.id"
            class="w-full form-control form-input form-input-bordered"
            :disabled="disabled"
            :value="field.value"
            maxlength="10"
            ref="datePicker"
            type="text"
            :placeholder="placeholder"
            @change="dateChange"
        />
    </div>
</template>

<script>
import flatpickr from 'flatpickr'
import 'flatpickr/dist/themes/airbnb.css'
export default {
    props: ['resourceName', 'field'],
    computed: {
            /*
            * Get resource id value from parent.
            */
            resourceId() {
                return this.$parent.resource.id.value;
            },
        },
    data: () => ({ 
        flatpickr: null,
        isValidDate: true,
    }),
    mounted() {
        this.$nextTick(() => {
            this.flatpickr = flatpickr(this.$refs.datePicker, {
                allowInput: true,
            })
        })
    },
    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || "";
        },
        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value)
        },
        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value;
        },
        dateChange(e) {
            this.isValidDate = this.checkValidDate(e.target.value);
            if (this.isValidDate) {
                const value = e.target.value;
                const fields = this.$parent.resource.fields;
                const index = fields.findIndex(function (field) { 
                    return field.name === e.target.name; 
                }); 
                this.$parent.resource.fields[index].value = e.target.value;
                let formData = new FormData();
                Object.keys(fields).forEach((key)=> {
                    if (fields[key].attribute !== this.field.attribute){
                        if (fields[key].belongsToId === undefined) {
                            formData.append(fields[key].attribute, fields[key].value);
                        }
                        else {
                            formData.append(fields[key].attribute, fields[key].belongsToId);
                        }
                    } else {
                        formData.append(fields[key].attribute, value);
                    }
                });
    
                formData.append('_method', 'PUT');
    
                return Nova.request().post(`/nova-api/${this.resourceName}/${this.resourceId}`, formData)
                    .then(() => {
                        this.$toasted.show(`${this.field.name} updated to "${value}"`, { type: 'success' });
                    }, (response) => {
                        this.$toasted.show(response, { type: 'error' });
                    })
            } else {
                this.$toasted.show(`${e.target.name} ${e.target.value} is not valid ('YYYY-MM-DD')`, { type: 'error' });
            }
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
        clear() {
        this.flatpickr.clear()
        },
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

