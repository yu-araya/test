<template>
    <b-message
        v-if="message.msg"
        title="message"
        :type="message.type"
        aria-close-label="Close message"
        class="alert-message"
    >
        {{ message.msg }}
    </b-message>
</template>

<script>
export default {
    name: "AlertMessage",
    props: {
        message: {},
    },
};
</script>
