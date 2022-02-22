/// <reference types="cypress" />
const date = require("../support/date");

context("喫食確認画面", () => {
    beforeEach(() => {
        cy.login("2", "0000");
        cy.get("#food-history-nav").click();
        cy.wait(5000);
    });

    before(() => {
        cy.task(
            "queryDb",
            `INSERT INTO food_history_infos VALUES
            ('1','2','1','','1','1','','${date.twoYearsAgo} 00:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('2','2','1','','2','2','','${date.twoYearsAgo} 01:00:00','0','350','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('3','2','1','','2','4','','${date.middleOfMonth1} 02:00:00','0','350','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('4','2','1','','1','1','','${date.middleOfMonth1} 03:00:00','2','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('5','2','1','','2','3','','${date.middleOfMonth2} 04:00:00','2','250','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('6','2','1','','2','3','','${date.middleOfMonth2} 05:00:00','2','250','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('7','2','1','','1','2','','${date.middleOfMonth2} 06:00:00','0','350','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('8','2','1','','1','1','','${date.middleOfMonth3} 07:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('9','2','1','','1','1','','${date.middleOfMonth3} 08:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('10','2','1','','1','1','','${date.middleOfMonth3} 09:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('11','2','1','','1','1','','${date.middleOfMonth3} 10:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('12','2','1','','1','1','','${date.middleOfMonth3} 11:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('13','2','1','','1','1','','${date.middleOfMonth3} 12:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('14','2','1','','1','1','','${date.middleOfMonth3} 13:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0'),
            ('15','2','1','','1','1','','${date.middleOfMonth3} 14:00:00','0','300','2020-06-17 11:18:35','2020-06-17 11:18:35','0');`
        );
        cy.task(
            "queryDb",
            `INSERT INTO reservation_infos VALUES
            ('1', '2', '1', '5', '', '${date.twoYearsAgo} 00:30:00', '0', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('2', '2', '2', '6', '', '${date.twoYearsAgo} 01:30:00', '0', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('3', '2', '1', '8', '', '${date.middleOfMonth1} 02:30:00', '2', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('4', '2', '2', '6', '', '${date.middleOfMonth1} 03:30:00', '0', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('5', '2', '1', '5', '', '${date.middleOfMonth1} 04:30:00', '0', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('6', '2', '1', '5', '', '${date.middleOfMonth1} 05:30:00', '2', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('7', '2', '1', '5', '', '${date.middleOfMonth2} 06:30:00', '2', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('8', '2', '1', '5', '', '${date.middleOfMonth2} 07:30:00', '2', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('9', '2', '1', '5', '', '${date.middleOfMonth2} 08:30:00', '2', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('10', '2', '1', '5', '', '${date.middleOfMonth2} 09:30:00', '0', '300', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('11', '2', '1', '6', '', '${date.middleOfMonth3} 10:30:00', '0', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('12', '2', '1', '6', '', '${date.middleOfMonth3} 11:30:00', '0', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('13', '2', '1', '6', '', '${date.middleOfMonth3} 12:30:00', '0', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0'),
            ('14', '2', '1', '6', '', '${date.middleOfMonth3} 13:30:00', '0', '350', '2020-06-30 11:07:06', '2020-06-30 11:07:06', '0');`
        );
    });

    it("ヘッダーの「喫食確認」をクリックすると喫食確認画面が表示される", () => {
        cy.get("#food-history-datepicker").should("exist");
        cy.get("#food-history-table").should("exist");
        cy.get(".table-header > div").each((element, index) => {
            switch (index) {
                case 0:
                    expect(element).to.contain("登録日");
                    break;
                case 1:
                    expect(element).to.contain("登録時間");
                    break;
                case 2:
                    expect(element).to.contain("事業所");
                    break;
                case 3:
                    expect(element).to.contain("食事名");
                    break;
                case 4:
                    expect(element).to.contain("金額");
                    break;
                default:
                    break;
            }
        });
    });

    it(`現在の日付が選択されていて、10件のレコードが表示されていること。
        ２ページ目をクリックすると10件のレコードが表示されていること。
        次へをクリックすると残りの5件のレコードが表示されていること。`, () => {
        cy.get("tbody > tr").should(($rows) => {
            expect($rows, "10件のレコードが表示されていること").to.have.length(10);
        });

        cy.get(":nth-child(2) > .pagination-link").click();
        cy.get("tbody > tr").should(($rows) => {
            expect($rows, "10件のレコードが表示されていること").to.have.length(10);
        });

        cy.get(".level-item > .pagination > .pagination-next").click();
        cy.get("tbody > tr").should(($rows) => {
            expect($rows, "5件のレコードが表示されていること").to.have.length(5);
        });

        cy.get("#food-history-datepicker").click();
        cy.get(".is-selected").should(($sel) => {
            expect($sel, "現在の月が選択されていること").to.contain(`${date.month}月`);
        });
    });

    it("2年前の１ヶ月後（２３ヶ月前までしか選択できないこと）", () => {
        cy.get("#food-history-datepicker").click();
        cy.get(".pagination-list > .field > .control > .select > select").select(`${date.year - 2}`);
        cy.wait(2000);
        cy.get(".is-selectable")
            .first()
            .should(($selectable) => {
                expect($selectable, "選択可能な最小年月が２年前の１ヶ月後であること").to.contain(
                    `${date.severalMonthsAfterNow(1)}月`
                );
            })
            .click();
        cy.get("tbody > tr").should(($rows) => {
            expect($rows, "4件のレコードが表示されていること").to.have.length(4);
        });
    });

    it("１年前の6月を選択したらアイコンと「データが存在しません。」が表示されていること", () => {
        cy.get("#food-history-datepicker").click();
        cy.get(".datepicker-header > .pagination > .pagination-previous").click();
        cy.get(".datepicker-months > :nth-child(6)").click();
        cy.get(".section > .icon > .mdi").should("exist");
        cy.get("p")
            .should("exist")
            .contains("データが存在しません。");
    });

    it("レコードの内容の確認", () => {
        cy.get("#total-cost").should(($cost) => {
            expect($cost, "合計金額が正しい値になっていること").to.contain("合計金額 : 5,450円");
        });
        cy.get("tbody > tr")
            .first()
            .should(($first) => {
                expect($first).not.have.class("food-history__table-deleted");
                expect($first.children(`td[data-label="登録日"]`)).to.contain("15");
                expect($first.children(`td[data-label="登録時間"]`)).to.contain("02:00:00");
                expect($first.children(`td[data-label="事業所"]`)).to.contain("工場");
                expect($first.children(`td[data-label="食事名"]`)).to.contain("丼");
                expect($first.children(`td[data-label="金額"]`)).to.contain("350円");
            })
            .next()
            .should(($next) => {
                expect($next).have.class("food-history__table-deleted");
                expect($next.children(`td[data-label="登録日"]`)).to.contain("15");
                expect($next.children(`td[data-label="登録時間"]`)).to.contain("02:30:00");
                expect($next.children(`td[data-label="事業所"]`)).to.contain("工場");
                expect($next.children(`td[data-label="食事名"]`)).to.contain("丼予約");
                expect($next.children(`td[data-label="金額"]`)).to.contain("350円");
            })
            .next()
            .should(($next) => {
                expect($next).have.class("food-history__table-deleted");
                expect($next.children(`td[data-label="登録日"]`)).to.contain("15");
                expect($next.children(`td[data-label="登録時間"]`)).to.contain("03:00:00");
                expect($next.children(`td[data-label="事業所"]`)).to.contain("本社");
                expect($next.children(`td[data-label="食事名"]`)).to.contain("定食");
                expect($next.children(`td[data-label="金額"]`)).to.contain("300円");
            })
            .next()
            .should(($next) => {
                expect($next).not.have.class("food-history__table-deleted");
                expect($next.children(`td[data-label="登録日"]`)).to.contain("15");
                expect($next.children(`td[data-label="登録時間"]`)).to.contain("03:30:00");
                expect($next.children(`td[data-label="事業所"]`)).to.contain("本社");
                expect($next.children(`td[data-label="食事名"]`)).to.contain("丼予約");
                expect($next.children(`td[data-label="金額"]`)).to.contain("350円");
            });
    });

    it("登録日をクリックすると降順にソートされること", () => {
        cy.get(".is-current-sort").click({ force: true });
        cy.get(`tbody > tr > td[data-label="登録日"]`)
            .first()
            .should("to.contain", "17");
        cy.get(`tbody > tr`)
            .first()
            .should(($first) => {
                expect($first).not.have.class("food-history__table-deleted");
                expect($first.children(`td[data-label="登録日"]`)).to.contain("17");
                expect($first.children(`td[data-label="登録時間"]`)).to.contain("14:00:00");
                expect($first.children(`td[data-label="事業所"]`)).to.contain("本社");
                expect($first.children(`td[data-label="食事名"]`)).to.contain("定食");
                expect($first.children(`td[data-label="金額"]`)).to.contain("300円");
            })
            .next()
            .should(($next) => {
                expect($next).not.have.class("food-history__table-deleted");
                expect($next.children(`td[data-label="登録日"]`)).to.contain("17");
                expect($next.children(`td[data-label="登録時間"]`)).to.contain("13:30:00");
                expect($next.children(`td[data-label="事業所"]`)).to.contain("本社");
                expect($next.children(`td[data-label="食事名"]`)).to.contain("丼予約");
                expect($next.children(`td[data-label="金額"]`)).to.contain("350円");
            });
    });

    it("事業所を2回クリックすると昇順にソートされること", () => {
        cy.get("thead > tr > :nth-child(3)")
            .click({ force: true })
            .click({ force: true });
        cy.get(`tbody > tr > td[data-label="事業所"]`)
            .first()
            .should("to.contain", "本社");
        cy.get(`tbody > tr`)
            .first()
            .should(($first) => {
                expect($first).have.class("food-history__table-deleted");
                expect($first.children(`td[data-label="登録日"]`)).to.contain("15");
                expect($first.children(`td[data-label="登録時間"]`)).to.contain("03:00:00");
                expect($first.children(`td[data-label="事業所"]`)).to.contain("本社");
                expect($first.children(`td[data-label="食事名"]`)).to.contain("定食");
                expect($first.children(`td[data-label="金額"]`)).to.contain("300円");
            })
            .next()
            .should(($next) => {
                expect($next).not.have.class("food-history__table-deleted");
                expect($next.children(`td[data-label="登録日"]`)).to.contain("15");
                expect($next.children(`td[data-label="登録時間"]`)).to.contain("03:30:00");
                expect($next.children(`td[data-label="事業所"]`)).to.contain("本社");
                expect($next.children(`td[data-label="食事名"]`)).to.contain("丼予約");
                expect($next.children(`td[data-label="金額"]`)).to.contain("350円");
            });
    });

    after(() => {
        cy.task("queryDb", `TRUNCATE food_history_infos;`);
        cy.task("queryDb", `TRUNCATE reservation_infos;`);
    });
});
