/// <reference types="cypress" />

context('ログイン', () => {
    beforeEach(() => {
      cy.visit('/TouchPay.standard/administrators/login')
    })

    it('ログインできる', () => {
        // https://on.cypress.io/type
        cy.get('#AdministratorLoginName').type('sysadmin')
        cy.get('#AdministratorPassword').type('initpasswd')
        cy.get('#AdministratorLoginForm').submit()

        // ログアウトが表示されている
        cy.contains('ログアウト')

        // メインメニューが表示されている
        cy.get('#main_menu').should('exist')
    })

    it('ログインIDなしでエラーになる', () => {
        cy.get('#AdministratorPassword').type('initpasswd')
        cy.get('#AdministratorLoginForm').submit()
        cy.contains('IDを入力してください')     
    })

    it('ログインパスワードなしでエラーになる', () => {
        cy.get('#AdministratorLoginName').type('sysadmin')
        cy.get('#AdministratorLoginForm').submit()
        cy.contains('パスワードを入力してください')     
    })

    it('ログインID/パスワードなしでエラーになる', () => {
        cy.get('#AdministratorLoginForm').submit()
        cy.contains('IDとパスワードを入力してください')     
    })

})
