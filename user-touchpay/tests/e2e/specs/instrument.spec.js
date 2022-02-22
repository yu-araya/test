/// <reference types="cypress" />

context("メイン画面", () => {
    beforeEach(() => {
        cy.login("2", "0000");
    });

    it("初期ログイン時の画面に事業所セレクトボックスとカレンダーが存在すること。", () => {
        cy.get("#instrument-select-box").should("exist");
        cy.get("#calendar-description").should("exist");
        cy.get("#reservation-calendar").should("exist");
    });

    it("事業所セレクトボックスで本社と工場が選択できること。", () => {
        cy.get("#instrument-select-box").should("have.value", "1");
        cy.get("#instrument-select-box")
            .select("工場")
            .should("have.value", "2");
    });
});
