import axios from "axios";
import axiosRetry, { isRetryableError } from "axios-retry";

const hostName = location.hostname;
const timeout = hostName === "www.touchpay.biz" ? 7000 : 30000;
const customAxios = axios.create({ timeout, headers: { "Content-Type": "application/x-www-form-urlencoded" } });

axiosRetry(customAxios, {
    retries: 2,
    shouldResetTimeout: true,
    retryDelay: (retryCount) => retryCount * 1000,
    retryCondition: isRetryableError,
});

export default customAxios;
