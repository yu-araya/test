<template>
    <section>
        <ValidationObserver ref="observer" v-slot="{ passes }">
            <div class="container tile is-ancester is-4">
                <div class="tile is-vertical is-parent ">
                    <div class="tile is-parent is-vertical has-text-left">
                        <div class="tile is-child">
                            <BValidationInput
                                id="change-password-input"
                                vid="confirmation"
                                :rules="{ required: 'required', max: 10, regex: /^[0-9|a-z|A-Z]+$/ }"
                                lavel="パスワード"
                                v-model="password"
                            ></BValidationInput>
                        </div>
                        <div class="tile is-child">
                            <BValidationInput
                                id="password-confirm-input"
                                :rules="{
                                    required: 'required',
                                    confirmed: 'confirmation',
                                    max: 10,
                                    regex: /^[0-9|a-z|A-Z]+$/,
                                }"
                                lavel="パスワード再入力"
                            ></BValidationInput>
                        </div>
                        <div class="tile is-child has-text-right">
                            <b-field horizontal>
                                <b-button id="change-password-button" type="is-primary" @click="passes(save)"
                                    >変更</b-button
                                >
                            </b-field>
                        </div>
                    </div>
                </div>
            </div>
        </ValidationObserver>
        <b-loading :is-full-page="true" :active.sync="isLoading" :can-cancel="false"> </b-loading>
    </section>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";
import BValidationInput from "@/components/common/BValidationInput.vue";
import { messages } from "@/assets/scripts/constant";
import { showConfirmDialog } from "@/assets/scripts/alert-dialog";

@Component({ components: { BValidationInput } })
export default class ChangePassword extends Mixins(StoreMixin) {
    private password = "";

    private isLoading = false;

    private async save() {
        showConfirmDialog(messages.confirm2.msg, messages.confirm2.type, async () => {
            this.isLoading = true;
            await this.changePassword(this.password);
            this.isLoading = false;
        });
    }
}
</script>

<style lang="scss">
.change-pass {
    &__container {
        width: 10vw;
    }
}
</style>
