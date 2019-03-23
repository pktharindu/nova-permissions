<template>
    <span class="w-full">
        <span
            v-for="(permission, option) in availableOptions"
            :key="permission.option"
            :class="optionClass(permission.option)"
            :title="permission.label"
            class="inline-block rounded-full w-2 h-2 mr-1"
        />
    </span>
</template>

<script>
export default {
    props: ['resourceName', 'field'],
    computed: {
        availableOptions() {
            return _.flatMap(this.field.options);
        },
    },
    methods: {
        optionClass(option) {
            return {
                'bg-success': this.field.value ? this.field.value.includes(option) : false,
                'bg-danger': this.field.value ? !this.field.value.includes(option) : true,
            }
        },
    },
}
</script>