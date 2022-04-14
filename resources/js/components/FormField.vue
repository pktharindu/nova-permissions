<template>

    <default-field class="nova-permissions" :field="field" :full-width-content="true">

        <template #field>

            <div class="w-full mb-6">

                <checkbox-with-label :checked="toggleChecked"
                                     class="leading-none"
                                     :class="{ indeterminate }"
                                     @input="toggleSelection()">

                    <span v-if="value.length > 0">
                        {{ __(':count selected', { count: value.length }) }}
                    </span>

                    <span v-else>
                        {{ __('Select all') }}
                    </span>

                </checkbox-with-label>

            </div>

            <div class="w-full flex flex-wrap -mx-2" v-if="field.withGroups">

                <div v-for="(permissions, group) in field.options"
                     :key="group"
                     class="mb-4 permission-group mx-2">

                    <h3 class="mb-3 font-bold text-lg">{{ __(group) }}</h3>

                    <div v-for="(permission, option) in permissions"
                         :key="permission.option"
                         class="flex mb-2">

                        <checkbox-with-label :value="permission.option"
                                             :checked="isChecked(permission.option)"
                                             @input="toggleOption(permission.option)"
                                             :title="permission.description">

                            {{ permission.label }}

                        </checkbox-with-label>

                    </div>

                </div>

            </div>

            <div class="w-full flex flex-wrap -mx-2" v-else>

                <div v-for="(permission, option) in field.options" :key="option" class="flex-auto mx-2 mb-2">

                    <checkbox :value="option"
                              :checked="isChecked(option)"
                              @input="toggleOption(option)"
                              class="pr-2"/>

                    <label :for="field.name"
                           v-text="permission.label"
                           @click="toggleOption(option)"
                           class="w-full"
                           :title="permission.description"/>

                </div>

            </div>

            <p v-if="hasError" class="my-2 text-danger">{{ firstError }}</p>

        </template>

    </default-field>

</template>

<script>

    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        mixins: [ FormField, HandlesValidationErrors ],
        props: [ 'resourceName', 'resourceId', 'field' ],
        computed: {
            indeterminate() {
                return this.toggleChecked
                    && this.value.length !== _.flatMap(this.field.options).length
            },
            toggleChecked() {
                return this.value.length > 0
            },
        },
        methods: {
            checkAll() {
                // With Groups
                if (this.field.withGroups) {
                    let permissions = _.flatMap(this.field.options)
                    for (var i = 0; i < permissions.length; i++) {
                        this.check(permissions[ i ].option)
                    }
                }
                // Todo: Without Groups
            },
            uncheckAll() {
                // With Groups
                if (this.field.withGroups) {
                    let permissions = _.flatMap(this.field.options)
                    for (var i = 0; i < permissions.length; i++) {
                        this.uncheck(permissions[ i ].option)
                    }
                }
                // Todo: Without Groups
            },
            toggleSelection() {
                // console.log(this.value)
                if (this.value.length) {
                    this.uncheckAll()
                } else {
                    this.checkAll()
                }
            },
            isChecked(option) {
                return this.value ? this.value.includes(option) : false
            },
            check(option) {
                if (!this.isChecked(option)) {
                    this.value.push(option)
                }
            },
            uncheck(option) {
                if (this.isChecked(option)) {
                    this.value = this.value.filter(item => item != option)
                }
            },
            toggleOption(option) {
                if (this.isChecked(option)) {
                    return this.uncheck(option)
                }
                this.check(option)
            },
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || []
            },
            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || [])
            },
            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },
        },
    }

</script>

<style lang="scss">

    .nova-permissions {

        .indeterminate > input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z' clip-rule='evenodd' /%3E%3C/svg%3E");
        }

        .permission-group {
            flex: 1 1 300px;
        }

    }

</style>
