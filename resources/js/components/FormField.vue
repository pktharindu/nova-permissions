<template>
  <default-field
    :field="field"
    :full-width-content="true"
  >
    <template slot="field">
      <div class="w-full mb-4">
        <button
          type="button"
          class="ml-auto btn btn-default mr-3"
          @click="checkAll()"
        >{{ __('Select all')}}</button>
        <button
          type="button"
          class="ml-auto btn btn-default"
          @click="uncheckAll()"
        >{{ __('Do not select any') }}</button>
      </div>
      <div
        class="w-full flex flex-wrap -mx-2"
        v-if="field.withGroups"
      >
        <div
          v-for="(permissions, group) in field.options"
          :key="group"
          class="mb-4 permission-group mx-2"
        >
          <h3 class="my-2">{{ __(group) }}</h3>
          <div
            v-for="(permission, option) in permissions"
            :key="permission.option"
            class="flex mb-2"
          >
            <checkbox
              :value="permission.option"
              :checked="isChecked(permission.option)"
              @input="toggleOption(permission.option)"
              class="mr-2"
            />
            <label
              :for="field.name"
              v-text="permission.label"
              @click="toggleOption(permission.option)"
              class="w-full"
              :title="permission.description"
            ></label>
          </div>
        </div>
      </div>
      <div
        class="w-full flex flex-wrap -mx-2"
        v-else
      >
        <div
          v-for="(permission, option) in field.options"
          :key="option"
          class="flex-auto mx-2 mb-2"
        >
          <checkbox
            :value="option"
            :checked="isChecked(option)"
            @input="toggleOption(option)"
            class="pr-2"
          />
          <label
            :for="field.name"
            v-text="permission.label"
            @click="toggleOption(option)"
            class="w-full"
            :title="permission.description"
          ></label>
        </div>
      </div>
      <p
        v-if="hasError"
        class="my-2 text-danger"
      >{{ firstError }}</p>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";
export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ["resourceName", "resourceId", "field"],
  methods: {
    checkAll() {
      // With Groups
      if (this.field.withGroups) {
        let permissions = _.flatMap(this.field.options);
        for (var i = 0; i < permissions.length; i++) {
          this.check(permissions[i].option);
        }
      }
      // Todo: Without Groups
    },
    uncheckAll() {
      // With Groups
      if (this.field.withGroups) {
        let permissions = _.flatMap(this.field.options);
        for (var i = 0; i < permissions.length; i++) {
          this.uncheck(permissions[i].option);
        }
      }
      // Todo: Without Groups
    },
    isChecked(option) {
      return this.value ? this.value.includes(option) : false;
    },
    check(option) {
      if (!this.isChecked(option)) {
        this.value.push(option);
      }
    },
    uncheck(option) {
      if (this.isChecked(option)) {
        this.$set(this, "value", this.value.filter(item => item != option));
      }
    },
    toggleOption(option) {
      if (this.isChecked(option)) {
        return this.uncheck(option);
      }
      this.check(option);
    },
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || [];
    },
    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.value || []);
    },
    /**
     * Update the field's internal value.
     */
    handleChange(value) {
      this.value = value;
    }
  }
};
</script>
