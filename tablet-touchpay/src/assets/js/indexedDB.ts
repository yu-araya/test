import Dexie from "dexie";
import { RequestData } from "@/store/types";

export class FoodHistoryInfos extends Dexie {
    logs: Dexie.Table<RequestData, number>; // number = type of the primkey

    constructor() {
        super("FoodHistoryInfos");
        this.version(1).stores({
            logs: "++",
        });
        this.logs = this.table("logs");
    }

    public async insertRequestData(requestData: RequestData) {
        await this.logs.add(requestData);
    }

    public async bulcInsertRequestData(requestDatas: RequestData[]) {
        await this.logs.bulkAdd(requestDatas);
    }

    public async deleteAllLogs() {
        await this.logs.clear();
    }

    public async selectAllLogs(): Promise<RequestData[]> {
        return await this.logs.toArray();
    }

    public async allLogsCount(): Promise<number> {
        return await this.logs.count();
    }
}

export class SoldOutInfos extends Dexie {
    soldouts: Dexie.Table<number[], number>;

    constructor() {
        super("SoldOutInfos");
        this.version(1).stores({
            soldouts: "++",
        });
        this.soldouts = this.table("soldouts");
    }

    public async insertSoldoutData(soldout: number[]) {
        await this.soldouts.clear();
        await this.soldouts.add(soldout);
    }

    public async selectSoldouts(): Promise<number[]> {
        const soldouts = await this.soldouts.toArray();
        return soldouts[0];
    }
}
