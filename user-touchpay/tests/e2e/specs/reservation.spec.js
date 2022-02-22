/// <reference types="cypress" />

const date = require("../support/date");
const wednesdayTag = `#calendar-cell-tag-${date.wednesday}`;
context("予約モーダル", () => {
    beforeEach(() => {
        cy.login("2", "0000");
        cy.get(":nth-child(2) > .vc-svg-icon").click();
    });

    it("モーダルを開くと予約メニューが表示されていること。", () => {
        cy.openModal();
        cy.get("#reserve-modal-card5")
            .should("exist")
            .contains("定食予約");
        cy.get("#reserve-modal-card5-counter").should("have.value", "0");
        cy.get("#reserve-modal-card6")
            .should("exist")
            .contains("丼予約");
        cy.get("#reserve-modal-card6-counter").should("have.value", "0");
    });

    it("キャンセルをクリックするとモーダルが閉じること。", () => {
        cy.openModal();
        cy.get("#reserve-modal-cancel").click();
        cy.get("#reserve-modal").should("not.exist");
    });

    it("+ボタンを押して数を増やした後にクリアボタンをクリックすると、全ての数がリセットされること。", () => {
        cy.openModal();
        cy.menuCountUp(5, 3);
        cy.get("#reserve-modal-card5-counter").should("have.value", "3");
        cy.menuCountUp(6, 2);
        cy.get("#reserve-modal-card6-counter").should("have.value", "2");
        cy.get("#reserve-modal-clear").click();
        cy.get("#reserve-modal-card5-counter").should("have.value", "0");
        cy.get("#reserve-modal-card6-counter").should("have.value", "0");
    });

    it(`+ボタンを押して数を増やした後に保存ボタンをクリックすると、メッセージが表示される。
            OKするとモーダルが閉じてカレンダーに予約有りが表示される。`, () => {
        cy.openModal();
        cy.menuCountUp(5, 3);
        cy.get("#reserve-modal-card5-counter").should("have.value", "3");
        cy.menuCountUp(6, 2);
        cy.get("#reserve-modal-card6-counter").should("have.value", "2");
        cy.saveReservation();
        cy.get(wednesdayTag).should("exist");
    });

    it("再びモーダルを表示すると保存した予約数が表示されていること。", () => {
        cy.openModal();
        cy.get("#reserve-modal-card5-counter").should("have.value", "3");
        cy.get("#reserve-modal-card6-counter").should("have.value", "2");
    });

    it(`事業所：本店で予約登録後に事業所：工場にすると予約有りが消える`, () => {
        cy.get("#instrument-select-box")
            .select("工場")
            .should("have.value", "2");
        cy.get(wednesdayTag).should("not.exist");
    });

    it(`クリアボタンでデータをクリアして保存するとモーダルが閉じてカレンダーから予約有りが消える`, () => {
        cy.get(wednesdayTag).should("exist");
        cy.openModal();
        cy.get("#reserve-modal-clear").click();
        cy.saveReservation();
        cy.get(wednesdayTag).should("not.exist");
    });
});
