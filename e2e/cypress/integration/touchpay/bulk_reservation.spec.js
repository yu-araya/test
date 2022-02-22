const dayjs = require('dayjs');

const now = dayjs(new Date());

const weekNumber = new Date().getDay();

let nextSunday = '';
if (weekNumber <= 3) {
    nextSunday = now.add(7 - (weekNumber), 'days');
} else {
    nextSunday = now.add(14 - (weekNumber), 'days');
}

context('予約一括登録', () => {
    beforeEach(() => {
        // ログイン
        cy.sysLogin()
        // 予約一括登録に遷移
        cy.get('[data-cy=bulk_reservation]').click({force: true})
    })

    before(() => {
        // テスト前に削除
        cy.task('queryDb', 'DELETE FROM reservation_infos')
    });

    it('予約食事区分が表示されていること', () => {

        cy.get('[data-cy=reserveFoodDivisionKey5]').should('have.html', '5')
        cy.get('[data-cy=reserveFoodDivisionBase5]').should('have.html', '本社')
        cy.get('[data-cy=reserveFoodDivisionValue5]').should('have.html', '定食予約')
        cy.get('[data-cy=reserveFoodDivisionKey6]').should('have.html', '6')
        cy.get('[data-cy=reserveFoodDivisionBase6]').should('have.html', '本社')
        cy.get('[data-cy=reserveFoodDivisionValue6]').should('have.html', '丼予約')
        cy.get('[data-cy=reserveFoodDivisionKey7]').should('have.html', '7')
        cy.get('[data-cy=reserveFoodDivisionBase7]').should('have.html', '工場')
        cy.get('[data-cy=reserveFoodDivisionValue7]').should('have.html', '定食予約')
        cy.get('[data-cy=reserveFoodDivisionKey8]').should('have.html', '8')
        cy.get('[data-cy=reserveFoodDivisionBase8]').should('have.html', '工場')
        cy.get('[data-cy=reserveFoodDivisionValue8]').should('have.html', '丼予約')

    })

    it('EXCELファイルをアップロードし、登録されること', () => {

        cy.uploadFile('予約一括登録__正常.xlsx', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('EXCELファイルのアップロードに成功しました。')

        // 予約データが登録されること
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 5 AND reservation_date = '2020-05-20 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 5 AND reservation_date = '2020-04-20 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(2)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 6 AND reservation_date = '2020-05-25 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 6 AND reservation_date = '2020-07-01 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(2)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 7 AND reservation_date = '2020-06-22 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 8 AND reservation_date = '2020-06-22 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(2)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '1' AND employee_kbn = '1' AND food_division = 8 AND reservation_date = '2020-07-01 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '2' AND employee_kbn = '1' AND food_division = 6 AND reservation_date = '2020-05-25 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(2)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '2' AND employee_kbn = '1' AND food_division = 6 AND reservation_date = '2020-04-20 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '2' AND employee_kbn = '1' AND food_division = 7 AND reservation_date = '2020-05-20 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(2)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '2' AND employee_kbn = '1' AND food_division = 7 AND reservation_date = '2020-07-01 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '3' AND employee_kbn = '1' AND food_division = 5 AND reservation_date = '2020-06-22 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })
        cy.task('queryDb', "SELECT * FROM reservation_infos WHERE employee_id = '3' AND employee_kbn = '1' AND food_division = 8 AND reservation_date = '2020-05-25 00:00:00'")
        .then(results => {
            expect(results).to.have.lengthOf(1)
        })

    })

    it('ファイルを選択せずにアップロードボタンを押すとメッセージが表示されること', () => {

        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードするファイルを選択してください。')

    })

    it('excelファイル内の値各種エラー', () => {

        cy.uploadFile('予約一括登録_エラー.xlsx', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('入力された社員コードは登録されていません。（2レコード目）')
        cy.contains('入力された社員コードは登録されていません。（3レコード目）')
        cy.contains('社員コードは半角英数字で入力してください。（4レコード目）')
        cy.contains('社員コードは10桁以内で入力してください。（5レコード目）')
        cy.contains('社員コードを入力してください。（6レコード目）')
        cy.contains('食事区分は5,6,7,8のいずれかで入力してください。（12レコード目）')
        cy.contains('食事区分は5,6,7,8のいずれかで入力してください。（13レコード目）')
        cy.contains('4/20の列の値が不正です。未入力または個数を入力して下さい。（16レコード目）')
        cy.contains('4/20の列の値が不正です。未入力または個数を入力して下さい。（17レコード目）')
        cy.contains('曜日の列は1件以上入力して下さい。（18レコード目）')
        cy.contains('4/19,4/21,4/24の列の値が不正です。未入力または個数を入力して下さい。（19レコード目）')

    })

    it('excelファイル内のカラムエラー', () => {

        cy.uploadFile('予約一括登録__カラムエラー.xlsx', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('4,6,7列目の日付が不正です。')

    })

    it('excelファイル以外アップロードしようとした際のエラー', () => {

        cy.uploadFile('example.json', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードできるファイルはEXCELファイルとなります。')

    })

    it('「翌週（翌々週）の予約状況」が表示されていること', () => {

        cy.get('[data-cy=reservationStatus]').should('have.html', nextSunday.format('YYYY年M月D日') + '週の予約状況')

    })

})
