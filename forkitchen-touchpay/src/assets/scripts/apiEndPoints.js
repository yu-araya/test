import { testMode } from "../property/property.js";
const touchPayUrl = (() => {
    if (!testMode) {
        const pathname = location.pathname.substr(0, location.pathname.indexOf("/forkitchen"));
        return `${location.protocol}//${location.hostname}${pathname}`;
    } else {
        return "http://localhost:8081/TouchPay.standard";
    }
})();

export const urls = {
    watchOrderUrl: (requestAll) => `${touchPayUrl}/app/api/watchOrder?requestAll=${requestAll}`,
    getMenusUrl: () => `${touchPayUrl}/app/api/getMenus`,
    getVersionUrl: (contents) => `${touchPayUrl}/app/api/getVersion?contents=${contents}`,
    infologUrl: () => `${touchPayUrl}/app/api/infolog`,
};
