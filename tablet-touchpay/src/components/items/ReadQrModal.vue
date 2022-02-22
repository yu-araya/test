<template>
    <section class="modal-card read-qr-modal">
        <header class="modal-card-head">
            カメラにQRコードが写る様に調整してください
            <b-button class="read-qr-modal__camera-button" @click="startFrontCamera">カメラ切り替え</b-button>
            <b-button class="read-qr-modal__reload-button" @click="reloadApp">アプリケーション再起動</b-button>
        </header>
        <section class="modal-card-body">
            <!-- <qrcode-stream :key="key" :camera="camera" @init="onCameraChange" @decode="onDecode"></qrcode-stream> -->
            <qrcode-stream :camera="camera" @init="onCameraChange" @decode="onDecode"></qrcode-stream>
        </section>
        <footer class="modal-card-foot">
            <b-button class="button" type="button" @click="$parent.close()">戻る</b-button>
        </footer>
    </section>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
//eslint-disable-next-line
//@ts-ignore
import { QrcodeStream } from "vue-qrcode-reader";
import MixinComponent from "@/assets/js/mixin";
import { messages } from "@/assets/js/constant";

@Component({
    components: { QrcodeStream },
})
export default class ReadQrModal extends Mixins(MixinComponent) {
    private camera = "front";

    private errorMessage = "";

    private errorCount = 0;

    // private key = 0;

    private startFrontCamera() {
        this.camera = this.camera === "front" ? "rear" : "front";
    }

    /**
     * アプリ再起動
     */
    private reloadApp(){
        // this.key = this.key ? 0 : 1 ;
        window.location.reload(true);
    }

    //eslint-disable-next-line
    private onCameraChange(promise: any) {
        promise.catch((error: Error) => {
            const cameraMissingError = error.name === "OverconstrainedError";
            const triedFrontCamera = this.camera === "front";

            if (triedFrontCamera && cameraMissingError) {
                this.camera = "auto";
            }
        });
    }

    private async onDecode(qrDecodeString: string) {
        this.camera = "off";
        const employeeId = await this.decodeUserId(qrDecodeString).catch((err) => {
            console.log("ReadQrModal -> onDecode -> err", err);
            return "";
        });
        const isCorrectEmployeeId = await this.getEmployeeIdWhenCorrectId(employeeId);
        const submitResult = await this.submitIfCorrect(employeeId, isCorrectEmployeeId);
        if (submitResult) {
            // モーダル消す
            //eslint-disable-next-line
            //@ts-ignore
            this.$parent.close();
        } else if (this.errorCount < 3) {
            // エラーが3回未満なら続行
            this.camera = "auto";
        } else {
            // エラーが3回に達したらメッセージを表示して終了
            this.playSound("error");
            this.createMessage(messages["error4"]);
            //eslint-disable-next-line
            //@ts-ignore
            this.$parent.close();
        }
    }

    // private async decodeUserId(qrDecodeString: string) {
    //     const base64DecodedString = atob(qrDecodeString);
    //     return base64DecodedString.replace("userId", "");
    // }

    private async submitIfCorrect(employeeId: string, isCorrectEmployeeId: boolean) {
        if (employeeId && isCorrectEmployeeId) {
            this.setLoading(true);
            await this.regist(employeeId);
            this.setLoading(false);
            return true;
        } else {
            this.errorCount++;
            return false;
        }
    }
}
</script>

<style lang="scss" scoped>
@import "@/assets/style/global.scss";
.read-qr-modal {
    width: 100%;
    &__camera-button {
        margin-left: auto;
    }
    &__reload-button {
        margin-left: auto;
    }
}
</style>
