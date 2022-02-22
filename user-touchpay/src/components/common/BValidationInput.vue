<template>
    <ValidationProvider :vid="vid" :rules="rules" v-slot="{ errors, valid }">
        <b-field
            :horizontal="horizontal"
            :label="lavel"
            :type="{ 'is-danger': errors[0], 'is-success': valid }"
            :message="errors"
        >
            <b-input :id="id" :type="type" v-model="innerValue"></b-input>
        </b-field>
    </ValidationProvider>
</template>

<script>
import { ValidationProvider } from "vee-validate";

export default {
    components: {
        ValidationProvider,
    },
    props: {
        id: {
            type: String,
            default: "",
        },
        vid: {
            type: String,
        },
        rules: {
            type: [Object, String],
            default: "",
        },
        lavel: {
            type: String,
        },
        // must be included in props
        value: {
            type: null,
        },
        type: {
            type: String,
            default: "is-primary",
        },
        horizontal: {
            type: Boolean,
        },
    },
    data: () => ({
        innerValue: "",
    }),
    watch: {
        // Handles internal model changes.
        innerValue(newVal) {
            this.$emit("input", newVal);
        },
        // Handles external model changes.
        value(newVal) {
            this.innerValue = newVal;
        },
    },
    created() {
        if (this.value) {
            this.innerValue = this.value;
        }
    },
};
</script>
