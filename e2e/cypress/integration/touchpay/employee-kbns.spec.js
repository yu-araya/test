/// <reference types="cypress" />

context('社員区分メンテナンス', () => {
    beforeEach(() => {
        // ログイン
        cy.sysLogin()
        // 食堂精算集計をクリック
        cy.get('[data-cy=employee-kbns]').click()
    })

    before(() => {
        // テストで作ったデータが残ってたら削除
        cy.task('queryDb', 'DELETE FROM employee_kbns WHERE employee_kbn = 34')
    })

    it('社員区分の新規登録ができる', () => {

        cy.get('[data-cy=addEmployeeKbn]').click()
        cy.get('[data-cy=employeeKbnId]').type('34')
        cy.get('[data-cy=employeeKbnName]').type('社長')
        cy.get('[data-cy=employee-kbn-add-form]').submit()
        cy.contains('登録を行いました。')
        cy.get('[data-cy=employee-kbns]').click()
        cy.get('[data-cy=employeeKbnId34]').should('have.html', '34')
        cy.get('[data-cy=employeeKbnName34]').should('have.html', '社長')

    })

    it('社員区分の不正文字種でエラー', () => {

        cy.get('[data-cy=addEmployeeKbn]').click()
        cy.get('[data-cy=employeeKbnId]').type('１＆')
        cy.get('[data-cy=employeeKbnName]').type('社長')
        cy.get('[data-cy=employee-kbn-add-form]').submit()
        cy.contains('社員区分は1〜99の間で入力してください。')

    })

    it('社員区分の新規登録時に社員区分重複でエラー', () => {

        cy.get('[data-cy=addEmployeeKbn]').click()
        cy.get('[data-cy=employeeKbnId]').type('34')
        cy.get('[data-cy=employeeKbnName]').type('エラー')
        cy.get('[data-cy=employee-kbn-add-form]').submit()
        cy.contains('社員区分が既に登録されています。')

    })

    it('社員区分名が修正できる', () => {

        cy.get('[data-cy=employeeKbnDetail34]').click(15, 15)
        cy.get('[data-cy=employeeKbnName').clear()
        cy.get('[data-cy=employeeKbnName]').type('会長')
        cy.get('[data-cy=employeeKbnUpdate]').click()
        cy.contains('更新を行いました。')
        cy.get('[data-cy=employeeKbnId34]').should('have.html', '34')
        cy.get('[data-cy=employeeKbnName34]').should('have.html', '会長')

    })

    it('社員区分の削除が正常に行われる', () => {

        cy.get('[data-cy=employeeKbnDetail34]').click(15, 15)
        cy.get('[data-cy=employeeKbnDelete]').click()
        cy.contains('削除を行いました。')

    })

    it('社員区分の削除の取り消しが正常に行われる', () => {

        cy.get('[data-cy=employeeKbnDetail34]').click(15, 15)
        cy.get('[data-cy=employeeKbnDeleteCancel]').click()
        cy.contains('更新を行いました。')

    })

})
