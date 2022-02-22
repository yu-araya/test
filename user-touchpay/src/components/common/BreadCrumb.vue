<template>
    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li v-for="(bread, idx) in breadcrumbList" :key="idx" :class="isActive(bread.active)">
                <router-link :to="{ name: bread.name }">{{ bread.title }}</router-link>
            </li>
        </ul>
    </nav>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";

@Component
export default class BreadCrumb extends Vue {
    private get breadcrumbList() {
        return this.$route.meta.breadcrumb;
    }

    private get isActive() {
        return (active: boolean) => {
            return active ? ["is-active"] : [];
        };
    }
}
</script>
