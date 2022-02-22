/// <reference types="cypress" />

context("ログイン", () => {
    it("ログイン可能。", () => {
        cy.login("2", "0000");
        cy.url().should("include", "/#/2/reservation");
        cy.get("#header-user-name").should("contain", "E2Eテスト用2");
    });

    it("別の人でもログイン可能", () => {
        cy.login("3", "0000");
        cy.url().should("include", "/#/3/reservation");
        cy.get("#header-user-name").should("contain", "E2Eテスト用3");
    });

    it("存在しないユーザー情報を入力するとエラーになる", () => {
        cy.visit("/#/login");
        cy.get("#loginIdInput").type("123123");
        cy.get("#loginPasswordInput").type("12312312");
        cy.get("#loginButton").click();
        cy.get(".media-content > p")
            .should("exist")
            .contains("有効な社員データが存在しません。");
    });
});
