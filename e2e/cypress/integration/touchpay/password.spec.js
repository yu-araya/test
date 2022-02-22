/// <reference types="cypress" />

context('パスワード変更', () => {
    beforeEach(() => {
        // 管理者テーブルを初期値に戻しておく
        cy.task('queryDb', 'DELETE FROM administrators')
        cy.task('queryDb', "INSERT INTO administrators VALUES (11,'sysadmin','initpasswd','1','2019-04-16 07:23:27','2019-04-16 07:23:27',0)")
        // ログイン
        cy.sysLogin()
        // パスワード変更画面へ遷移
        cy.get('[data-cy=password]').click()
    })

    it('パスワード変更を正常に行い、新パスワードでログインができる事', () => {

        cy.get('[data-cy=inPassWord]').type('testpass')
        cy.get('[data-cy=againPassWord]').type('testpass')
        cy.get('[data-cy=changePassWord]').submit()
        cy.get('[data-cy=logOut]').click()
        cy.visit("TouchPay.standard/administrators/login")
        cy.get('#AdministratorLoginName').type(Cypress.env('sysuser'))
        cy.get('#AdministratorPassword').type('testpass')
        cy.get('#AdministratorLoginForm').submit()
        cy.contains('予約状況照会')

    })

    it('パスワード変更を正常に行い、旧パスワードでログインが出来ない事', () => {

        cy.get('[data-cy=inPassWord]').type('testpass')
        cy.get('[data-cy=againPassWord]').type('testpass')
        cy.get('[data-cy=changePassWord]').submit()
        cy.get('[data-cy=logOut]').click()
        cy.sysLogin()
        cy.contains('IDまたはパスワードが違います。')

    })

    it('パスワード変更でどちらも空欄エラー', () => {

        cy.get('[data-cy=changePassWord]').submit()
        cy.contains('パスワードを入力してください。')

    })

    it('パスワード変更で入力と再入力の不一致エラー', () => {

        cy.get('[data-cy=inPassWord]').type('testpass')
        cy.get('[data-cy=againPassWord]').type('testpas')
        cy.get('[data-cy=changePassWord]').submit()
        cy.contains('入力された二つのパスワードが一致しません。')

    })

})
