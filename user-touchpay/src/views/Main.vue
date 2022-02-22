<template>
    <div class="main h100">
        <router-view />
    </div>
</template>

<script lang="ts">
// @ is an alias to /src
import { Component, Vue } from "vue-property-decorator";

@Component
export default class Main extends Vue {}
</script>

<style lang="scss">
.main {
    padding-top: 2em;
}
</style>
