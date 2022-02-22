/// <reference types="cypress" />

const date = require("../support/date");
context("カレンダー", () => {
    before(() => {
        // 最初にtruncateしとく必要あり
        cy.task("queryDb", `TRUNCATE day_off_calendars;`);
        cy.task(
            "queryDb",
            `INSERT INTO day_off_calendars VALUES 
                (1, 1, '${date.twoDaysAgo}', '2020-04-01 12:00:00', '2020-04-01 12:00:00'),
                (2, 1, '${date.sunday}', '2020-04-01 12:00:00', '2020-04-01 12:00:00');`
        );
    });

    beforeEach(() => {
        cy.login("2", "0000");
        cy.get(".vc-title").should(($title) => {
            expect($title, "現在の月が表示されていること。").contain(`${date.month}月 ${date.year}`);
        });
    });

    it("休日設定の確認", () => {
        cy.get(":nth-child(2) > .vc-svg-icon").click();
        cy.get(`#calendar-${date.sunday}`).should(($date) => {
            expect($date, "休日設定が適用されていること").have.class("holiday");
        });
        cy.openModal(true);
        cy.get("#reserve-modal").should("not.exist");
    });

    it("モーダルが表示されること。", () => {
        cy.get(":nth-child(2) > .vc-svg-icon").click();
        cy.openModal();
        cy.get("#reserve-modal").should("exist");
    });

    it(`年月ナビゲーションのテスト`, () => {
        cy.get(".vc-title").click();
        cy.get(".vc-popover-content").should("exist");
        cy.get(".vc-w-12")
            .contains(`${date.month}月`)
            .should(($selected) => {
                expect($selected, "現在の月が選択されていること。").have.class("vc-grid-focus");
            });
        cy.get(".vc-w-12")
            .contains(`${date.month - 1}月`)
            .click();
        cy.get(".vc-title").should(($title) => {
            expect($title, "現在から１ヶ月前の月が表示されていること。").contain(
                `${date.severalMonthsBeforeNow(1)}月 ${date.year}`
            );
        });
    });

    it("前月ナビゲーションのテスト", () => {
        cy.get(":nth-child(1) > .vc-svg-icon").click();
        cy.get(".vc-title").should(($title) => {
            expect($title, "現在から１ヶ月前の月が表示されていること。").contain(
                `${date.severalMonthsBeforeNow(1)}月 ${date.year}`
            );
        });
    });

    it("次月ナビゲーションのテスト", () => {
        cy.get(":nth-child(2) > .vc-svg-icon").click();
        cy.get(".vc-title").should(($title) => {
            expect($title, "現在から１ヶ月前の月が表示されていること。").contain(
                `${date.severalMonthsAfterNow(1)}月 ${date.year}`
            );
        });
    });

    it("過去日セルのテスト", () => {
        cy.get(`#calendar-${date.yesterday}`)
            .should(($yesterday) => {
                expect($yesterday, "前日の日付セルが非活性の色になっていること。").have.css(
                    "background-color",
                    "rgb(170, 170, 170)"
                );
                expect($yesterday, "前日の日付セルが休日の文字色になっていないこと。").not.have.css("color", "#ff3a3a");
            })
            .click();
        cy.get("#reserve-modal").should(($modal) => {
            expect($modal, "昨日の日付をクリックしてもモーダルが表示されないこと。").not.exist;
        });
        cy.get(`#calendar-${date.twoDaysAgo}`).should(($twoDaysAgo) => {
            expect($twoDaysAgo, "2日前の日付セルが非活性の色になっていること。").have.css(
                "background-color",
                "rgb(170, 170, 170)"
            );
            expect($twoDaysAgo, "2日前の日付セルが休日の文字色になっていること。").have.css(
                "color",
                "rgb(255, 58, 58)"
            );
        });
    });

    it("本日日付セルのテスト", () => {
        cy.get(`#calendar-${date.today}`)
            .should(($today) => {
                expect($today, "本日の日付セルが非活性の色になっていないこと。").not.have.css(
                    "background-color",
                    "rgb(170, 170, 170)"
                );
                expect($today, "本日の日付セルが休日の文字色になっていないこと。").not.have.css(
                    "color",
                    "rgb(255, 58, 58)"
                );
            })
            .click();
        cy.get("#reserve-modal").should(($modal) => {
            expect($modal, "本日の日付をクリックするとモーダルが表示されること。").exist;
        });
    });
});
