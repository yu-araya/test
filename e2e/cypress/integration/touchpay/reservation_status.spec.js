/// <reference types="cypress" />
const dayjs = require('dayjs');
let now = dayjs(new Date());

context('予約状況照会', () => {
    beforeEach(() => {
      cy.sysLogin()
    })

    before(() => {
        // データ削除
        cy.task('queryDb', 'DELETE FROM reservation_infos')
    });

    it('事業所の横に本社、工場がラベル表記されていること', () => {

        cy.get('[data-cy=baseKbn]').should('have.text', '本社')
        cy.get('[data-cy=reservation2]').click()
        cy.get('[data-cy=baseKbn]').should('have.text', '工場')

    })

    it('対象年月が表示されること', () => {

        cy.dateCheckTwoYearsLater(now, '#ReservationInfoTargetDateYear', '#ReservationInfoTargetDateMonth')

    })

    it('社員名で検索ができ、クリックで社員コード欄に社員コードが入力されること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=employeeSearch]').click({force: true})
        cy.get('[data-cy=inputEmployeeName]').type('正カード正常社員', {force: true})
        cy.contains('1：正カード正常社員').click({force: true})
        cy.get('[data-cy=inputEmployeeId]').should('have.value', '1')

    })

    it('社員コードに対応した社員名が表示されること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=inputEmployeeId]').type('1', {force: true})
        cy.get('[data-cy=caretOut]').click({force: true})
        cy.get('[data-cy=displayEmployeeName]').should('have.value', '正カード正常社員')
        // cy.contains('正カード正常社員')
    })

    it('本社には本社のメニュー、工場には工場のメニューが選択できること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=menu]').select('定食予約', {force: true})
        cy.get('[data-cy=menu]').select('丼予約' ,{force: true})

        cy.get('[data-cy=reservation2]').click()
        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=menu]').select('定食予約', {force: true})
        cy.get('[data-cy=menu]').select('丼予約', {force: true})

    })

    it('予約登録ができること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=inputEmployeeId]').type('1', {force: true})
        cy.get('[data-cy=addMemo]').type('予約登録ができること', {force: true})
        cy.get('[data-cy=addReservation]').submit()

        cy.get('[data-cy=reservationId0]').should('have.text', '1')
        cy.get('[data-cy=reservationName0]').should('have.text', '正カード正常社員')
        cy.get('[data-cy=reservationFood0]').should('have.text', '定食予約')
        cy.get('[data-cy=reservationMemo0]').should('have.value', '予約登録ができること')

    })

    it('予約の削除ができること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=reservationDelete0]').click({force: true})
        cy.get('[data-cy=reservationRefrect0]').click()
        cy.contains('削除を行いました。')

    })

    it('予約の削除取り消しができること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=reservationFix0]').click({force: true})
        cy.get('[data-cy=reservationRefrect0]').click()
        cy.contains('更新を行いました。')

    })

    it('予約の備考を修正できること', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=reservationFix0]').click({force: true})
        cy.get('[data-cy=reservationMemo0]').clear()
        cy.get('[data-cy=reservationMemo0]').type('予約の備考を修正できること', {force: true})
        cy.get('[data-cy=reservationRefrect0]').click()
        cy.contains('更新を行いました。')
        cy.get('[data-cy=reservationMemo0]').should('have.value', '予約の備考を修正できること')

    })

    it('存在しない社員コードエラー', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=inputEmployeeId]').type('347347', {force: true})
        cy.get('[data-cy=addMemo]').type('存在しない社員コードエラー', {force: true})
        cy.get('[data-cy=addReservation]').submit()
        cy.contains('入力された社員コードは登録されていません。')

    })

    it('社員コード文字種エラー', () => {

        cy.get('[data-cy=count14]').click({force: true})
        cy.get('[data-cy=inputEmployeeId]').type('あいうえお', {force: true})
        cy.get('[data-cy=addMemo]').type('社員コード文字種エラー', {force: true})
        cy.get('[data-cy=addReservation]').submit()
        cy.contains('社員コードは半角英数字で入力してください。')

    })

    // it('pdfで献立表がアップできること', () => {

    //     cy.uploadFile('献立表.pdf', '[data-cy=selectFile]')
    //     cy.get('[data-cy=uploadFile]').submit().wait(3000)
    //     cy.contains('ファイルのアップロードに成功しました。')
    //     cy.get('[data-cy=menuLink]').should('have.text', now.format('M') + '月の献立表')
    //     cy.fixture('../../../TouchPay.standard/app/webroot/menu/' + now.format('YYYYMM') + '.pdf')

    // })

    it('pdf以外アップした時のエラー', () => {

        cy.uploadFile('example.json', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードできるファイルはPDFファイルのみとなります。')

    })

})
