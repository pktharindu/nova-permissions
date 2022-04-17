<template>

    <panel-item class="nova-permissions" :field="field">

      <template #value>

        <div class="flex">

            <div class="w-full flex flex-wrap -mx-2" v-if="field.withGroups">

                <div v-for="(permissions, group) in field.options" :key="group" class="mb-4 permission-group mx-2">

                    <h3 class="mb-3 font-bold text-lg">{{ __(group) }}</h3>

                    <div v-for="(permission, option) in permissions"
                         :key="option"
                         class="flex-auto"
                         :title="permission.description">

                        <span class="inline-block rounded-full w-2 h-2 mr-1" :class="optionClass(permission.option)"/>
                        <span>{{ permission.label }}</span>

                    </div>

                </div>

            </div>

            <div class="w-full flex flex-wrap -mx-2" v-else>

                <div v-for="(permission, option) in field.options"
                     :key="option"
                     class="flex-auto mx-2"
                     :title="permission.description">

                    <div class="inline-block rounded-full w-2 h-2 mr-1" :class="optionClass(option)"/>

                    <div>{{ permission.label }}</div>

                </div>

            </div>

        </div>

      </template>

    </panel-item>

</template>

<script>

    export default {
        props: [ 'resource', 'resourceName', 'resourceId', 'field' ],
        methods: {
            optionClass(option) {
                return {
                    'bg-green-500': this.field.value
                        ? this.field.value.includes(option)
                        : false,
                    'bg-red-500': this.field.value
                        ? !this.field.value.includes(option)
                        : true,
                }
            },
        },
    }

</script>

<style lang="scss">

    .nova-permissions .permission-group {
        flex: 1 1 300px;
    }

</style>
