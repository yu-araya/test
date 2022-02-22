context("タブレットタッチペイ", () => {
    beforeEach(() => {
        cy.visit("/#/Order");
    });

    it("売り切れメニューボタンを押すと、売り切れメニューが表示されること", () => {
        // ・売り切れメニューボタンをクリックして保存すると、対象のメニューが売り切れになる事
        cy.get("[data-cy=soldOutSetting]").click({ force: true });
        cy.get("[data-cy=soldOutFood1]").click();
        cy.get("[data-cy=saveSoldOut]").click();
        cy.get("[data-cy=menuButton1]")
            .should("have.text", "定食売り切れ")
            .and("be.disabled");

        // ・もう一度押して保存すると売り切れが解除される事
        cy.get("[data-cy=soldOutSetting]").click({ force: true });
        cy.get("[data-cy=soldOutFood1]").click();
        cy.get("[data-cy=saveSoldOut]").click();
        cy.get("[data-cy=menuButton1]").should("have.text", "定食¥300");
    });

    it("タブをクリックしたらメニューが切り替わる事", () => {
        cy.get('[class="tab-item"]')
            .get('[style="display: none;"]')
            .should("have.text", "定食¥300丼¥320定食予約¥300")
            .and("not.have.text", "定食¥300丼¥350");
        cy.get("ul > :nth-child(2) > a").click(400, 15);
        cy.get('[class="tab-item"]')
            .get('[style="display: none;"]')
            .should("have.text", "定食¥300丼¥350")
            .and("not.have.text", "定食¥300丼¥320定食予約¥300");
    });

    it("メニューをクリックしたら、右の注文表示に追加される事", () => {
        cy.get("[data-cy=menuButton1]").click();
        cy.get("[data-cy=orderCount1]").should("have.value", "1");

        // ・カードをタッチしてくださいロゴが表示される事
        cy.contains("2.カードを読取端末にタッチしてください");

        // ・同じメニューをクリックしたら右の注文表示の個数が増える事
        cy.get("[data-cy=menuButton1]").click();
        cy.get("[data-cy=orderCount1]").should("have.value", "2");

        // ・合計金額が変わる事
        cy.get("[data-cy=totalCost]").should("have.text", "600円");

        // ・同じメニューを連打して、上限になった際にインフォメッセージが表示される事
        for (let i = 0; i < 8; i++) {
            cy.get("[data-cy=menuButton1]").click();
        }
        cy.contains("一度に注文できるのは1メニュー9個までです。");
    });

    it("違うメニューをクリックしたら注文カードが増える事", () => {
        cy.get("[data-cy=menuButton1]").click();
        cy.get("[data-cy=totalCost]").should("have.text", "300円");
        cy.get("[data-cy=menuButton2]").click();

        // ・合計金額が変わる事
        cy.get("[data-cy=totalCost]").should("have.text", "650円");
    });

    it("注文表示の＋をクリックしたら個数が1増える事", () => {
        cy.get("[data-cy=menuButton1]").click();
        cy.get("[data-cy=orderCount1]").should("have.value", "1");
        cy.get('[class="mdi mdi-plus"]').click();
        cy.get("[data-cy=orderCount1]").should("have.value", "2");

        // ・さらに＋を連打して上限値以上にはならないこと
        for (let i = 0; i < 14; i++) {
            cy.get('[class="mdi mdi-plus"]').click();
        }
        cy.get("[data-cy=orderCount1]").should("have.value", "9");

        // ・合計金額が変わる事
        cy.get("[data-cy=totalCost]").should("have.text", "2,700円");
    });

    it("注文表示のーをクリックしたら個数が1減る事", () => {
        for (let i = 0; i < 9; i++) {
            cy.get("[data-cy=menuButton1]").click();
        }

        cy.get('[class="mdi mdi-minus"]').click();
        cy.get("[data-cy=orderCount1]").should("have.value", "8");

        // ・さらにーを連打して0以下にはならない事
        for (let i = 0; i < 14; i++) {
            cy.get('[class="mdi mdi-minus"]').click();
        }
        cy.get("[data-cy=orderCount1]").should("have.value", "0");

        // ・合計金額が変わる事
        cy.get("[data-cy=totalCost]").should("have.text", "0円");
    });

    it("カードタッチをしたら注文が完了する事", () => {
        cy.task("queryDb", "DELETE FROM food_history_infos");

        cy.get("[data-cy=menuButton1]").click();
        cy.get("[data-cy=menuButton2]").click();
        cy.get("[data-cy=orderCount1]").type("{enter}", { force: true });
        cy.server();
        cy.route("POST", "/TouchPay.standard/api/foodhistory").as("foodHistory");
        cy.wait("@foodHistory");
        cy.get(".media-content")
            .should("exist")
            .contains("ご注文ありがとうございました。");
    });

    it("クリアボタンを押すと注文と合計金額がクリアされる事", () => {
        for (let i = 0; i < 5; i++) {
            cy.get("[data-cy=menuButton1]").click();
            cy.get("[data-cy=menuButton2]").click();
        }
        cy.get("[data-cy=selectClear]").click();
        cy.get('[data-cy="orderCard1"]').should("not.exist");
        cy.get('[data-cy="orderCard2"]').should("not.exist");
        cy.get("[data-cy=totalCost]").should("have.text", "0円");
    });
});
