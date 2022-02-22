import dayjs from "dayjs";

export const now = dayjs().locale("ja");

export const today = now.format("YYYY-MM-DD");
export const yesterday = now.subtract(1, "day").format("YYYY-MM-DD");
export const twoDaysAgo = now.subtract(2, "day").format("YYYY-MM-DD");
export const tomorrow = now.add(1, "day").format("YYYY-MM-DD");

export const year = now.format("YYYY");
export const month = now.format("M");

export const middleOfMonth1 = now.format("YYYY-MM-15");
export const middleOfMonth2 = now.format("YYYY-MM-16");
export const middleOfMonth3 = now.format("YYYY-MM-17");
export const day = Number(dayjs(middleOfMonth1).format("d"));

export const nextMonthDate = now.add(1, "month");
export const nextYear = nextMonthDate.format("YYYY");
export const nextMonth = nextMonthDate.format("M");

export const nextMonthMiddle = nextMonthDate.format("YYYY-MM-15");
export const sunday = dayjs(nextMonthMiddle)
    .subtract(day, "day")
    .format("YYYY-MM-DD");
export const wednesday = dayjs(sunday)
    .add(3, "day")
    .format("YYYY-MM-DD");
export const twoYearsAgo = nextMonthDate
    .subtract(2, "year")
    .startOf("month")
    .format("YYYY-MM-DD");

export const severalMonthsBeforeNow = (delta) => {
    return now.subtract(delta, "month").format("M");
};
export const severalMonthsAfterNow = (delta) => {
    return now.add(delta, "month").format("M");
};
