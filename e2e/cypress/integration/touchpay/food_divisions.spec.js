/// <reference types="cypress" />

context('食事メンテナンス', () => {
    beforeEach(() => {
        // ログイン
        cy.sysLogin()
        // 食事メンテナンスをクリック
        cy.get('[data-cy=food-divisions]').click()
    })

    before(() => {
        // データ削除(途中で試験中断の際に)
        cy.task('queryDb', 'DELETE FROM food_periods')
    });

    it('メニュー登録ができる', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteDate]').type('2020-02-23', {force: true})
        cy.get('[data-cy=foodMainteName]').type('焼きそば')
        cy.get('[data-cy=foodMainteValue]').type('200')
        cy.get('[data-cy=foodMainteAdd]').click(15, 15)
        cy.contains('食事期間を登録しました。')

        cy.get('[data-cy=foodDateFix0]').should('have.value', '2020-02-23')
        cy.get('[data-cy=foodNameFix0]').should('have.value', '焼きそば')
        cy.get('[data-cy=foodValueFix0]').should('have.value', '200')

    })

    it('メニュー登録時の日付空欄エラー', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteName]').type('焼きそば')
        cy.get('[data-cy=foodMainteValue]').type('200')
        cy.get('[data-cy=foodMainteAdd]').click(15, 15)
        cy.contains('開始日を選択してください。')

    })

    it('メニュー登録時の食事名空欄エラー', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteDate]').type('2020-02-23', {force: true})
        cy.get('[data-cy=foodMainteValue]').type('200')
        cy.get('[data-cy=foodMainteAdd]').click(15, 15)
        cy.contains('食事名は1から50文字の間で入力してください')

    })

    it('メニュー登録時の価格空欄エラー', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteDate]').type('2020-02-23', {force: true})
        cy.get('[data-cy=foodMainteName]').type('焼きそば')
        cy.get('[data-cy=foodMainteAdd]').click(15, 15)
        cy.contains('価格は1から7桁の間の数値で入力してください')

    })

    it('メニュー登録時の金額文字種エラー', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteDate]').type('2020-02-23', {force: true})
        cy.get('[data-cy=foodMainteName]').type('焼きそば')
        cy.get('[data-cy=foodMainteValue]').type('あああああ')
        cy.get('[data-cy=foodMainteAdd]').click(15, 15)
        cy.contains('価格は1から7桁の間の数値で入力してください')

    })

    it('メニュー登録時の開始日重複エラー', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteDate]').type('2020-02-23', {force: true})
        cy.get('[data-cy=foodMainteName]').type('重複油そば')
        cy.get('[data-cy=foodMainteValue]').type('1000')
        cy.get('[data-cy=foodMainteAdd]').click(15, 15)
        cy.contains('開始日が重複しています。')

    })

    it('メニュー修正ができる', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteFix0]').click()
        cy.get('[data-cy=foodDateFix0]').clear({force: true})
        cy.get('[data-cy=foodDateFix0]').type('2020-03-01', {force: true})
        cy.get('[data-cy=foodNameFix0]').clear()
        cy.get('[data-cy=foodNameFix0]').type('修正そば')
        cy.get('[data-cy=foodValueFix0]').clear()
        cy.get('[data-cy=foodValueFix0]').type('600')
        cy.get('[data-cy=foodMainteRevision0]').click()
        cy.contains('食事期間を更新しました。')

        cy.get('[data-cy=foodDateFix0]').should('have.value', '2020-03-01')
        cy.get('[data-cy=foodNameFix0]').should('have.value', '修正そば')
        cy.get('[data-cy=foodValueFix0]').should('have.value', '600')

    })

    it('メニュー削除ができる', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteDelete0]').click()
        cy.get('[data-cy=foodMainteRevision0]').click()
        cy.contains('食事期間を削除しました。')

    })

    it('メニュー削除の取り消しができる', () => {

        cy.get('[data-cy=foodDetail1]').click(15, 15)
        cy.get('[data-cy=foodMainteFix0]').click()
        cy.get('[data-cy=foodNameFix0]').clear()
        cy.get('[data-cy=foodNameFix0]').type('削除取り消し丼')
        cy.get('[data-cy=foodMainteRevision0]').click()
        cy.contains('食事期間を更新しました。')

    })

    it('メニューの一括アップロードができる', () => {

        cy.uploadFile('メニュー一括登録.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('CSVファイルのアップロードに成功しました。')

    })

    it('最新のメニュー名と金額が表示されている事', () => {

        cy.get('[data-cy=foodMenu1]').should(el => expect(el.text().trim()).to.equal('削除取り消し丼'))
        cy.get('[data-cy=foodCost1]').should(el => expect(el.text().trim()).to.equal('600円'))

        cy.get('[data-cy=foodMenu2]').should(el => expect(el.text().trim()).to.equal('肉うどん'))
        cy.get('[data-cy=foodCost2]').should(el => expect(el.text().trim()).to.equal('240円'))

        cy.get('[data-cy=foodMenu3]').should(el => expect(el.text().trim()).to.equal('チキンカレー'))
        cy.get('[data-cy=foodCost3]').should(el => expect(el.text().trim()).to.equal('180円'))

        cy.get('[data-cy=foodMenu4]').should(el => expect(el.text().trim()).to.equal('ビーフカレー'))
        cy.get('[data-cy=foodCost4]').should(el => expect(el.text().trim()).to.equal('300円'))

        cy.get('[data-cy=foodMenu5]').should(el => expect(el.text().trim()).to.equal('うどん'))
        cy.get('[data-cy=foodCost5]').should(el => expect(el.text().trim()).to.equal('160円'))

        cy.get('[data-cy=foodMenu6]').should(el => expect(el.text().trim()).to.equal('きつねうどん'))
        cy.get('[data-cy=foodCost6]').should(el => expect(el.text().trim()).to.equal('200円'))

        cy.get('[data-cy=foodMenu7]').should(el => expect(el.text().trim()).to.equal('たぬきうどん'))
        cy.get('[data-cy=foodCost7]').should(el => expect(el.text().trim()).to.equal('220円'))

        cy.get('[data-cy=foodMenu8]').should(el => expect(el.text().trim()).to.equal('らあめん'))
        cy.get('[data-cy=foodCost8]').should(el => expect(el.text().trim()).to.equal('240円'))

    })

    it('開始年月日の重複', () => {

        cy.uploadFile('メニュー一括登録.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('開始年月日が重複しています。（1レコード目）')
        cy.contains('開始年月日が重複しています。（2レコード目）')
        cy.contains('開始年月日が重複しています。（3レコード目）')
        cy.contains('開始年月日が重複しています。（4レコード目）')
        cy.contains('開始年月日が重複しています。（5レコード目）')
        cy.contains('開始年月日が重複しています。（6レコード目）')
        cy.contains('開始年月日が重複しています。（7レコード目）')
        cy.contains('開始年月日が重複しています。（8レコード目）')

    })

    it('メニューのアップロードの際のファイル未選択エラー', () => {

        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードするファイルを選択してください。')

    })

    it('メニューのアップロードの際のCSV以外のファイル選択エラー', () => {

        cy.uploadFile('example.json', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードできるファイルはCSVファイルのみとなります。')

    })

    it('メニューのアップロードの際の値エラー', () => {

        cy.uploadFile('メニュー一括登録エラー.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('食事区分は1,2,3,4,5,6,7,8のいずれかで入力してください。（1レコード目）')
        cy.contains('開始日が不正です、正しい年月日を入力して下さい。（2レコード目）')
        cy.contains('金額は半角数字で入力してください。（3レコード目）')

    })
    
    after(() => {
        // テストで作ったデータ削除(全テスト一斉実施の際後続の試験に影響を与えないようにするため)
        cy.task('queryDb', 'DELETE FROM food_periods')
    })

})
