import dayjs from "dayjs";

export const getNowDayjs = () => {
    return dayjs().locale("ja");
};

export const formatDateString = (dateString, formatType) =>
    dayjs(dateString)
        .locale("ja")
        .format(formatType);
