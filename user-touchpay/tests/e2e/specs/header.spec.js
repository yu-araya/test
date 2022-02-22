/// <reference types="cypress" />

context("ヘッダーナビゲーション", () => {
    beforeEach(() => {
        cy.login("2", "0000");
    });

    it("ヘッダーにロゴ・ユーザー名・喫食確認画面・予約確認画面・パスワード変更・ログアウトが表示されている。", () => {
        cy.get("#logo").should("exist");
        cy.get("#header-user-name").should("contain", "E2Eテスト用2");
        cy.get("#food-history-nav").should("contain", "喫食確認画面");
        cy.get("#reservation-nav").should("contain", "予約画面");
        cy.get("#change-pass-nav").should("contain", "パスワード変更");
        cy.get("#logout-nav").should("contain", "ログアウト");
    });

    it("ロゴ・ユーザー名をクリックしても画面遷移しないこと。", () => {
        cy.get("#logo").click();
        cy.url().should("include", "/#/2/reservation");

        cy.get("#header-user-name").click();
        cy.url().should("include", "/#/2/reservation");
    });

    it("喫食確認画面・予約画面・パスワード変更画面をクリックするとそれぞれの画面に遷移すること。", () => {
        cy.get("#food-history-nav").click({ force: true });
        cy.url().should("include", "/#/2/foodHistory");

        cy.get("#reservation-nav").click({ force: true });
        cy.url().should("include", "/#/2/reservation");

        cy.get("#change-pass-nav").click({ force: true });
        cy.url().should("include", "/#/2/password");
    });

    // it("ログアウトをクリックするとダイアログが表示され、OKを押すとログイン画面に戻ること。ログ以外が表示されていないこと。", () => {
    //     cy.get("#logout-nav").click();
    //     cy.get(".media-content > p").should("contain", "ログアウトします。よろしいですか？");
    //     cy.get(".is-info").click();
    //     cy.url().should("include", "/#/login");

    //     cy.get("#logo").should("exist");
    //     cy.get("#header-user-name").should("not.exist", "正カード正常社員");
    //     cy.get("#food-history-nav").should("not.exist", "喫食確認画面");
    //     cy.get("#reservation-nav").should("not.exist", "予約画面");
    //     cy.get("#change-pass-nav").should("not.exist", "パスワード変更");
    //     cy.get("#logout-nav").should("not.exist", "ログアウト");
    // });
});
