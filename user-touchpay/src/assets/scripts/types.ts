type UserInfo = {
    id: string;
    name: string;
    password: string;
};

type Options = {
    userReservationFlg: boolean;
    userFoodHistoryFlg: boolean;
    qrcodeFlg: boolean;
};

type InstrumentDivision = {
    division: number;
    name: string;
};

type HolidayData = {
    instrumentDivision: number;
    holidayDate: string;
};

type FoodDivision = {
    instrumentDivision: number;
    division: number;
    name: string;
};

type ReservationData = {
    instrumentDivision: number;
    reservationDate: string;
    foodDivision: number;
    count: number;
};

type FoodHistoryData = {
    instrumentDivision: number;
    instrumentName: string;
    foodDivision: number;
    foodDivisionName: number;
    foodCost: number;
    cardReceptTime: Date;
    deleteFlg: boolean;
};

export { UserInfo, Options, InstrumentDivision, HolidayData, FoodDivision, ReservationData, FoodHistoryData };
