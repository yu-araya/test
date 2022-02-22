interface TabList {
    tabNum: string;
    tabName: string;
}

interface InstrumentList {
    instrument_division: number;
    instrument_name: string;
}

interface EmployeeData {
    created: string;
    delete_flg: string;
    dining_license_flg: string;
    dining_licensed_date: Date;
    employee_id: string;
    employee_kbn: string;
    employee_name1: string;
    employee_name2: string;
    ic_card_number: string;
    ic_card_number2: string;
    iccard_valid_e_time: Date;
    iccard_valid_e_time2: Date;
    iccard_valid_s_time: Date;
    iccard_valid_s_time2: Date;
    id: string;
    memo: string;
    modified: Date;
    password: string;
}

interface MenuData {
    category: string;
    food_division: number;
    food_division_name: string;
    instrument_division: number;
    food_cost: number;
    count: number;
    dispCard: boolean;
}

interface MessageData {
    isMessageModalActive: boolean;
    message: string;
    messageType: string;
}

interface RequestData {
    employeeId: string;
    instrumentDivision: string;
    order: OrderData[];
    cardReceptDateTime: Date;
}

interface OrderData {
    foodDivision: number;
    count: number;
    foodCost: number;
}

interface MessageConst {
    msg: string;
    type: string;
}

type MessageModalData = {
    isMessageModalActive: boolean;
    message: string;
    messageType: string;
};

export {
    TabList,
    InstrumentList,
    EmployeeData,
    MenuData,
    MessageData,
    RequestData,
    OrderData,
    MessageConst,
    MessageModalData,
};
