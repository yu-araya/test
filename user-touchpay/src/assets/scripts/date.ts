import dayjs from "dayjs";

/**
 * 現在の日付をフォーマットして返却
 * デフォルトのフォーマット "YYYY/MM/DD
 *
 * @param type
 */
export const nowDateString = (type = "YYYY/MM/DD"): string => {
    return dayjs(new Date())
        .locale("ja")
        .format(type);
};

/**
 * 日付形式文字列をフォーマットして返却
 * デフォルトのフォーマット "YYYY/MM/DD
 *
 * @param date
 * @param type
 */
export const formatDateString = (date: string, type = "YYYY/MM/DD"): string => {
    return dayjs(new Date(date))
        .locale("ja")
        .format(type);
};

/**
 * Date型の日付をフォーマットして文字列で返却
 * デフォルトのフォーマット "YYYY/MM/DD
 *
 * @param date
 * @param type
 */
export const formatDateToString = (date: Date, type = "YYYY/MM/DD"): string => {
    return dayjs(date)
        .locale("ja")
        .format(type);
};

/**
 * 日付文字列を受け取り曜日を返却
 *
 * @param date
 */
export const getDayOfWeek = (date: Date): string => {
    const week = ["日", "月", "火", "水", "木", "金", "土"];
    const dateNum = Number(formatDateToString(date, "d"));
    return week[dateNum];
};

/**
 * 現在の日付から差分年前の日付を取得
 *
 * @param delta
 */
export const severalYearsBeforeNow = (delta: number): Date => {
    const now = new Date();
    now.setFullYear(now.getFullYear() - delta);
    return now;
};

/**
 * 現在の日付から差分年後の日付を取得
 *
 * @param delta
 */
export const severalYearsAfterNow = (delta: number): Date => {
    const now = new Date();
    now.setFullYear(now.getFullYear() + delta);
    return now;
};

/**
 * 現在の日付から差分月前の日付を取得
 *
 * @param delta
 */
export const severalMonthsBeforeNow = (delta: number): Date => {
    const now = new Date();
    now.setMonth(now.getMonth() - delta);
    return now;
};

/**
 * 現在の日付から差分月後の日付を取得
 *
 * @param delta
 */
export const severalMonthsAfterNow = (delta: number): Date => {
    const now = new Date();
    now.setMonth(now.getMonth() + delta);
    return now;
};

/**
 * その月の最終日を取得
 *
 * @param date
 */
export const lastDayOfMonth = (date: string = nowDateString()): Date => {
    return dayjs(new Date(date))
        .locale("ja")
        .endOf("month")
        .toDate();
};

/**
 * 指定した年月かどうか判定
 *
 * @param target 日付形式文字列
 * @param comparison YYYY/MM
 */
export const isMonthly = (target: Date, comparison: Date): boolean => {
    return target?.getFullYear() === comparison?.getFullYear() && target?.getMonth() === comparison?.getMonth();
};

/**
 * 本日よりまえの日付か判定
 * @param targetDateStr
 */
export const isPastDate = (targetDateStr: string): boolean =>{
    const target = dayjs(targetDateStr).locale("ja");
    const now = dayjs(nowDateString()).locale("ja");
    return target.isBefore(now);
}
