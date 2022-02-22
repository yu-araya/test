/// <reference types="cypress" />

context("オプションによる画面の表示非表示", () => {
    it("予約オプションのみの場合", () => {
        cy.task("queryDb", `UPDATE options SET option_state=1 WHERE option_id=1;`);
        cy.task("queryDb", `UPDATE options SET option_state=0 WHERE option_id=2;`);
        cy.login("2", "0000");
        cy.url().should("include", "/#/2/reservation");
        cy.get("#food-history-nav").should("not.exist", "喫食確認画面");
        cy.get("#reservation-nav").should("contain", "予約画面");
    });

    it("喫食確認オプションのみの場合", () => {
        cy.task("queryDb", `UPDATE options SET option_state=0 WHERE option_id=1;`);
        cy.task("queryDb", `UPDATE options SET option_state=1 WHERE option_id=2;`);
        cy.login("2", "0000", `loadFoodHistory?employeeId=2`);
        cy.url().should("include", "/#/2/foodHistory");
        cy.get("#food-history-nav").should("contain", "喫食確認画面");
        cy.get("#reservation-nav").should("not.exist", "予約画面");
    });

    after(() => {
        cy.task("queryDb", `UPDATE options SET option_state=1 WHERE option_id=1;`);
        cy.task("queryDb", `UPDATE options SET option_state=1 WHERE option_id=2;`);
    });
});
