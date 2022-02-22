<template>
    <div id="app">
        <div class="header">
            <Header />
        </div>
        <div class="contents">
            <router-view />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import Header from "@/components/common/Header.vue";

@Component({
    components: {
        Header,
    },
})
export default class App extends Vue {}
</script>

<style lang="scss">
@import "@/assets/style/global.scss";
#app {
    font-family: "ヒラギノ丸ゴ ProN W4";
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;

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
</style>
