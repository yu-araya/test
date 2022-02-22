<template>
    <div id="app">
        <div class="header">
            <Header />
        </div>
        <div class="contents">
            <router-view />
        </div>
        <div class="loading">
            <b-modal :active.sync="loading" width="60vw" scroll="keep">
                <b-message :type="loadingMessage.type" has-icon aria-close-label="Loading message" :closable="false">
                    {{ loadingMessage.msg }}
                </b-message>
            </b-modal>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import Header from "@/components/common/Header.vue";
import MixinComponent from "@/assets/js/mixin";
import { messages } from "@/assets/js/constant";

@Component({ components: { Header } })
export default class App extends Mixins(MixinComponent) {
    async created() {
        this.setLoading(true);
        await this.getAppVersion();
        this.setNextTimer();
        this.setLoading(false);
    }

    get loadingMessage() {
        return messages.loading;
    }

    get loading() {
        return this.isLoading;
    }
}
</script>

<style lang="scss">
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;

    .header {
        height: 4.25rem;
    }

    .contents {
        height: 80vh;
    }
}

#nav {
    padding: 30px;

    a {
        font-weight: bold;
        color: #2c3e50;

        &.router-link-exact-active {
            color: #42b983;
        }
    }
}

.h100 {
    height: 100%;
}
</style>
