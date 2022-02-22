import { required, confirmed, max, regex } from "vee-validate/dist/rules";
import { extend } from "vee-validate";

extend("required", {
    ...required,
    message: "必須入力です。",
});

extend("confirmed", {
    ...confirmed,
    message: "パスワードが一致しません。",
});

extend("max", {
    ...max,
    message: "パスワードは10桁以内で入力してください。",
});

extend("regex", {
    ...regex,
    message: "半角英数字で入力して下さい。",
});
