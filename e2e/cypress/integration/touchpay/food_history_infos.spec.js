/// <reference types="cypress" />
const dayjs = require('dayjs');
let now = dayjs(new Date());

context('喫食登録/修正/削除', () => {
    beforeEach(() => {
      cy.resetdb()
      cy.sysLogin()
      // 社員別食堂精算をクリック
      cy.get('[data-cy=food-history-infos]').click()
    })

    it('検索年月日の値が正しい', () => {
        
        cy.get('[data-cy=food-history-infos-date]').should('exist')

        cy.dateCheck(now)

    })

    it('喫食の新規登録・修正・削除が正常にできる', () => {

        cy.addFoodHistory('1', '本社', '定食', now.format('YYYY-MM-DD'), '12:00', '喫食の定食の新規登録が正常にできる')
        cy.contains('登録を行いました。')
        cy.searchFoodHistory(now.add(-1, 'months').format('YYYY'), now.format('MM'), 1, '')

        cy.get('[data-cy=food-history-info-1]').find('[data-cy=basekbn-0]').should('have.html', '1')
        cy.get('[data-cy=detail-1]').click()

        cy.get('[data-cy=line0]').find('[data-cy=base-kbn]').contains('本社')
        cy.get('[data-cy=line0]').find('[data-cy=food-division]').contains('定食')
        cy.get('[data-cy=line0]').find('[data-cy=reason]').should('have.value', '喫食の定食の新規登録が正常にできる')

        // 更新処理
        cy.get('[data-cy=line0]').find('[data-cy=update-check]').check()
        cy.get('[data-cy=line0]').find('[data-cy=reason]').should('not.be.disabled')
        cy.get('[data-cy=line0]').find('[data-cy=reason]').clear()
        cy.get('[data-cy=line0]').find('[data-cy=reason]').type('喫食の修正が正常にできる')
        cy.get('[data-cy="food-history-detail-form-line0"]').submit()
        cy.contains('更新を行いました。')
        cy.get('[data-cy=line0]').find('[data-cy=reason]').should('have.value', '喫食の修正が正常にできる')

        // 削除処理
        cy.get('[data-cy=line0]').find('[data-cy=delete-check]').check()
        cy.get('[data-cy="food-history-detail-form-line0"]').submit()
        cy.contains('削除を行いました。')

    })

    it('検索条件が正常に稼働する', () => {
        // 先月の1日で追加する
        cy.addFoodHistory('1', '本社', '定食', now.add(-1, 'months').format('YYYY-MM-01'), '12:00', '検索年月で検索できる')
        cy.searchFoodHistory(now.add(-1, 'months').format('YYYY'), now.add(-1, 'months').format('MM'), 1, '')
        cy.get('[data-cy=food-history-info-1]').find('[data-cy=basekbn-0]').should('have.html', '1')
        cy.get('[data-cy=detail-1]').click()
        cy.get('[data-cy=line0]').find('[data-cy=reason]').should('have.value', '検索年月で検索できる')

        // 社員コードで検索する
        cy.addFoodHistory('2', '本社', '定食', now.format('YYYY-MM-DD'), '12:00', '社員コードで検索できる')
        cy.searchFoodHistory(now.format('YYYY'), now.format('MM'), 2, '')
        cy.get('[data-cy=food-history-info-2]').find('[data-cy=basekbn-0]').should('have.html', '1')
        cy.get('[data-cy=detail-2]').click()
        cy.get('[data-cy=line0]').find('[data-cy=reason]').should('have.value', '社員コードで検索できる')

        // 氏名で検索する
        cy.addFoodHistory('3', '本社', '定食', now.format('YYYY-MM-DD'), '12:00', '氏名で検索できる')
        cy.searchFoodHistory(now.format('YYYY'), now.format('MM'), '', 'E2Eテスト用3')
        cy.get('[data-cy=food-history-info-3]').find('[data-cy=basekbn-0]').should('have.html', '1')
        cy.get('[data-cy=detail-3]').click()
        cy.get('[data-cy=line0]').find('[data-cy=reason]').should('have.value', '氏名で検索できる')
    })

    it('検索結果なし', () => {
        // 検索年月ヒットなし
        cy.searchFoodHistory(now.add(-1, 'year').format('YYYY'), now.format('MM'), '', '')
        cy.contains('登録内容はありません')

        // 社員IDヒットなし
        cy.searchFoodHistory(now.format('YYYY'), now.format('MM'), '4', '')
        cy.contains('登録内容はありません')

        // 社員名ヒットなし
        cy.searchFoodHistory(now.format('YYYY'), now.format('MM'), '', 'E2Eテスト4')
        cy.contains('登録内容はありません')
    })

    it('喫食登録時の各種エラー', () => {
        cy.addFoodHistory('', '本社', '定食', now.format('YYYY-MM-DD'), '12:00', '社員IDなし')
        cy.contains('社員コードを入力してください。')
        cy.addFoodHistory('1', '本社', '定食', '', '12:00', '登録日なし')
        cy.contains('カード受付時間（日付）を入力してください。')
        cy.addFoodHistory('1', '本社', '定食', now.format('YYYY-MM-DD'), '', '登録時刻なし')
        cy.contains('カード受付時間（時刻）を入力してください。')
        cy.addFoodHistory('1', '本社', '定食', '20200101', '12:00', '登録日不正')
        cy.contains('カード受付時間（日付）に存在しない日付が設定されています。')
        cy.addFoodHistory('1', '本社', '定食', now.format('YYYY-MM-DD'), '1200', '登録時刻不正')
        cy.contains('カード受付時間（時刻）に存在しない時刻が設定されています。')
    })

})
