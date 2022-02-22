/// <reference types="cypress" />

context("パスワード変更画面", () => {
    it("ヘッダーの「パスワード変更」をクリックするとパスワード変更画面に遷移すること。", () => {
        cy.login("3", "0000");
        cy.get("#change-pass-nav").click();
        cy.url().should("include", "/#/3/password");
    });

    it("パスワードが変更できること。", () => {
        cy.login("3", "0000");
        cy.get("#change-pass-nav").click();
        cy.changePass("1234");
        cy.get(".media-content > p").should("contain", "パスワードを変更しました。");
    });

    it("変更前のパスワードでログインしようとするとエラーになること。", () => {
        cy.visit("/#/login");
        cy.get("#loginIdInput").type("3");
        cy.get("#loginPasswordInput").type("0000");
        cy.get("#loginButton").click();
        cy.get(".media-content > p").should("contain", "有効な社員データが存在しません。");
    });

    it("変更後のパスワードでログイン可能であること。", () => {
        cy.login("3", "1234");
        cy.get("#change-pass-nav").click();
        cy.changePass("0000");
        cy.get(".media-content > p").should("contain", "パスワードを変更しました。");
    });
});
