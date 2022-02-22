/// <reference types='cypress' />
import 'dayjs/locale/ja';

const dayjs = require('dayjs');
dayjs.locale('ja');
const now = dayjs(new Date());
const lastYear = now.add(-1, 'year').set('month', 11);

context('食堂精算集計', () => {
    beforeEach(() => {
      // 喫食管理と予約管理を削除する
      cy.resetdb()
      // ログイン
      cy.sysLogin()
      // 食堂精算集計をクリック
      cy.get('[data-cy=food-history-infos-sumdaily]').click()
    })

    it('事業所の値が正しい', () => {
        cy.get('[data-cy=baseKbnList]').should('exist')

        cy.baseCheck()
    })

    it('検索年月日の値が正しい', () => {

        cy.get('[data-cy=food-history-infos-sumdaily-date]').should('exist')

        cy.dateCheck(now)

    })

    it('日付/喫食数/小計/合計が正しい事', () => {

        // テストデータ登録処理
        cy.addFoodHistory('1', '本社', '定食', now.format('YYYY-MM-02'), '12:00', '定食のテストデータ登録(2日)')
        cy.addFoodHistory('1', '本社', '丼', now.format('YYYY-MM-02'), '12:00', '丼のテストデータ登録(2日)')
        for (let i = 0; i < 3; i++) {
            cy.addFoodHistory('1', '本社', '定食', now.format('YYYY-MM-01'), '12:00', '定食のテストデータ登録(1日)')
        }
        for (let i = 0; i < 2; i++) {
            cy.addFoodHistory('1', '本社', '丼', now.format('YYYY-MM-01'), '12:00', '丼のテストデータ登録(1日)')
        }
        cy.addFoodHistory('1', '工場', '定食', lastYear.format('YYYY-MM-31'), '12:00', '工場定食のテストデータ登録(去年)')
        cy.addFoodHistory('1', '工場', '丼', lastYear.format('YYYY-MM-31'), '12:00', '工場丼のテストデータ登録(去年)')
        for (let i = 0; i < 3; i++) {
            cy.addFoodHistory('1', '工場', '定食', lastYear.format('YYYY-MM-30'), '12:00', '工場定食のテストデータ登録(去年)')
        }
        for (let i = 0; i < 2; i++) {
            cy.addFoodHistory('1', '工場', '丼', lastYear.format('YYYY-MM-30'), '12:00', '工場丼のテストデータ登録(去年)')
        }

        // 食堂精算集計をクリック
        cy.get('[data-cy=food-history-infos-sumdaily]').click()

        // 1日の定食の数をチェック
        cy.get('[data-cy=foodMenu011]').should('have.html', '3')
        // 1日の丼の数をチェック
        cy.get('[data-cy=foodMenu012]').should('have.html', '2')
        // 1日の定食予約の数をチェック
        cy.get('[data-cy=foodMenu015]').should('have.html', '0')
        // 1日の丼予約の数をチェック
        cy.get('[data-cy=foodMenu016]').should('have.html', '0')
        // 2日の定食の数をチェック
        cy.get('[data-cy=foodMenu021]').should('have.html', '1')
        // 2日の丼の数をチェック
        cy.get('[data-cy=foodMenu022]').should('have.html', '1')
        // 2日の定食予約の数をチェック
        cy.get('[data-cy=foodMenu025]').should('have.html', '0')
        // 2日の丼予約の数をチェック
        cy.get('[data-cy=foodMenu026]').should('have.html', '0')
        // 1日の日付をチェック
        cy.get('[data-cy=dayNichi01]').should('have.html', '1')
        // 2日の日付をチェック
        cy.get('[data-cy=dayNichi02]').should('have.html', '2')
        // 1日の曜日をチェック
        cy.get('[data-cy=weekYoubi01]').should('have.html', now.set('date', 1).format('ddd'))
        // 2日の曜日をチェック
        cy.get('[data-cy=weekYoubi02]').should('have.html', now.set('date', 2).format('ddd'))
        // 1日の小計をチェック
        cy.get('[data-cy=subTotal01]').should('have.html', '1,600')
        // 2日の小計をチェック
        cy.get('[data-cy=subTotal02]').should('have.html', '650')
        // 定食の合計をチェック
        cy.get('[data-cy=totalCount1]').should('have.html', '4')
        // 丼の合計をチェック
        cy.get('[data-cy=totalCount2]').should('have.html', '3')
        // 定食予約の合計をチェック
        cy.get('[data-cy=totalCount5]').should('have.html', '0')
        // 丼予約の合計をチェック
        cy.get('[data-cy=totalCount6]').should('have.html', '0')
        // 合計金額をチェック
        cy.get('[data-cy=totalCost]').should('have.html', '2,250')

        // 検索条件切り替え
        cy.get('#FoodHistoryInfoBaseKbn').select('工場').wait(3000)
        cy.get('#FoodHistoryInfoCardReceptTimeYear').select('2019').wait(3000)
        cy.get('#FoodHistoryInfoCardReceptTimeMonth').select('12').wait(3000)

        // 30日の工場定食の数をチェック
        cy.get('[data-cy=foodMenu303]').should('have.html', '3')
        // 30日の工場丼の数をチェック
        cy.get('[data-cy=foodMenu304]').should('have.html', '2')
        // 30日の工場定食予約の数をチェック
        cy.get('[data-cy=foodMenu307]').should('have.html', '0')
        // 30日の工場丼予約の数をチェック
        cy.get('[data-cy=foodMenu308]').should('have.html', '0')
        // 31日の工場定食の数をチェック
        cy.get('[data-cy=foodMenu313]').should('have.html', '1')
        // 31日の工場丼の数をチェック
        cy.get('[data-cy=foodMenu314]').should('have.html', '1')
        // 31日の工場定食予約の数をチェック
        cy.get('[data-cy=foodMenu317]').should('have.html', '0')
        // 31日の工場丼予約の数をチェック
        cy.get('[data-cy=foodMenu318]').should('have.html', '0')
        // 30日の日付をチェック
        cy.get('[data-cy=dayNichi30]').should('have.html', '30')
        // 31日の日付をチェック
        cy.get('[data-cy=dayNichi31]').should('have.html', '31')
        // 30日の曜日をチェック
        cy.get('[data-cy=weekYoubi30]').should('have.html', lastYear.set('date', 30).format('ddd'))
        // 31日の曜日をチェック
        cy.get('[data-cy=weekYoubi31]').should('have.html', lastYear.set('date', 31).format('ddd'))
        // 30日の小計をチェック
        cy.get('[data-cy=subTotal30]').should('have.html', '1,540')
        // 31日の小計をチェック
        cy.get('[data-cy=subTotal31]').should('have.html', '620')
        // 工場定食の合計をチェック
        cy.get('[data-cy=totalCount3]').should('have.html', '4')
        // 工場丼の合計をチェック
        cy.get('[data-cy=totalCount4]').should('have.html', '3')
        // 工場定食予約の合計をチェック
        cy.get('[data-cy=totalCount7]').should('have.html', '0')
        // 工場丼予約の合計をチェック
        cy.get('[data-cy=totalCount8]').should('have.html', '0')
        // 合計金額をチェック
        cy.get('[data-cy=totalCost]').should('have.html', '2,160')
    })
})
