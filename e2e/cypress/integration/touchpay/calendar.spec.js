/// <reference types="cypress" />
const dayjs = require('dayjs');
let now = dayjs(new Date());

context('カレンダーメンテナンス', () => {
    beforeEach(() => {
        cy.sysLogin()

        cy.get('[data-cy=calendars]').click({force: true})
    })
    
    before(() => {
        // テストで作ったデータが残ってたら削除
        cy.task('queryDb', 'DELETE FROM day_off_calendars')
    });

    it('検索年月日の値が正しい', () => {

        cy.dateCheckTwoYearsLater(now, '#DayOffCalendarTargetDateYear', '#DayOffCalendarTargetDateMonth')

    })

    it('休日登録ができる事', () => {

        cy.get('[data-cy=date12]').click().wait(3000)
        cy.get('[data-cy=date12]').should('have.css', 'color').and('eq', 'rgb(255, 0, 0)')
        cy.get('[data-cy=reservation1]').click().wait(3000)
        cy.get('[data-cy=date12]').should('have.css', 'color').and('eq', 'rgb(255, 0, 0)')

    })

    it('休日登録の解除ができる事', () => {

        cy.get('[data-cy=date12]').click().wait(3000)
        cy.get('[data-cy=date12]').should('have.css', 'color').and('eq', 'rgb(0, 0, 0)')
        cy.get('[data-cy=reservation1]').click().wait(3000)
        cy.get('[data-cy=date12]').should('have.css', 'color').and('eq', 'rgb(51, 51, 51)')

    })

    it('CSVファイルアップロードにより一括登録ができる事', () => {

        cy.uploadFile('休日を一括設定.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit().wait(3000)
        cy.contains('CSVファイルのアップロードに成功しました。')
        cy.get('#DayOffCalendarTargetDateYear').select('2021').wait(3000)
        cy.get('#DayOffCalendarTargetDateMonth').select('01').wait(3000)
        cy.get('[data-cy=date1]').should('have.css', 'color').and('eq', 'rgb(255, 0, 0)')
        cy.get('[data-cy=date2]').should('have.css', 'color').and('eq', 'rgb(255, 0, 0)')
        cy.get('[data-cy=date3]').should('have.css', 'color').and('eq', 'rgb(255, 0, 0)')
        cy.get('[data-cy=date4]').should('have.css', 'color').and('eq', 'rgb(255, 0, 0)')

    })

    it('CSVファイル以外のアップロードによりエラー', () => {

        cy.uploadFile('example.json', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit().wait(3000)
        cy.contains('アップロードできるファイルはCSVファイルのみとなります。')

    })

    // it('CSVファイル内の不正な値によりエラー', () => {

    //     cy.uploadFile('カレンダー値エラー.csv', '[data-cy=selectFile]')
    //     cy.get('[data-cy=uploadFile]').submit().wait(3000)
    //     cy.contains('休日に存在しない日付が設定されています。（0レコード目）')

    // })

})
