/// <reference types="cypress" />
const dayjs = require('dayjs');
let yesterday = dayjs(new Date()).format('YYYY-MM-01');
let tomorrow = dayjs(new Date()).format('YYYY-MM-03');

context('社員情報メンテナンス', () => {
    beforeEach(() => {
        // ログイン
        cy.sysLogin()
        // 社員別食堂精算をクリック
        cy.get('[data-cy=employee-infos]').click({force: true})
    })

    before(() => {
        // テストで作ったデータが残ってたら削除(afterで消す前に中断した際、本テストに影響を与えないようにするため)
        cy.task('queryDb', 'DELETE FROM employee_infos WHERE employee_id = 347')
        cy.task('queryDb', 'DELETE FROM employee_infos WHERE employee_id = 164')
        cy.task('queryDb', 'DELETE FROM employee_infos WHERE employee_id = 157')
    })

    it('社員情報の新規登録ができる', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeId]').type('347')
        cy.get('[data-cy=cyEmployeeName]').type('テスト太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('1234567890')
        cy.get('[data-cy=useTermStart]').type(yesterday)
        cy.get('[data-cy=useTermEnd]').type(tomorrow)
        cy.get('[data-cy=cyCardNo2]').type('0987654321')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録ができる')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('登録を行いました。')
        cy.get('[data-cy=employee-infos]').click()
        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeId347]').should('have.html', '347')
        cy.get('[data-cy=employeeName347]').should('have.html', 'テスト太郎')

    })

    it('社員IDで社員情報が検索できる', () => {

        // 社員情報メンテナンスに遷移
        cy.get('[data-cy=employee-infos]').click()

        // 社員IDで社員情報が検索できるかチェック
        cy.get('[data-cy=cyEmployeeId]').type('347')
        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeId347]').should('have.html', '347')
    //
    })

    it('氏名で社員情報が検索できる', () => {

        cy.get('[data-cy=cyEmployeeName]').type('テスト太郎')
        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeName347]').should('have.html', 'テスト太郎')

    })

    it('条件なしで社員情報が検索できる', () => {

        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeId347]').should('have.html', '347')
        cy.get('[data-cy=employeeName347]').should('have.html', 'テスト太郎')

    })

    it('社員情報の新規登録時に社員IDの重複でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeId]').type('347')
        cy.get('[data-cy=cyEmployeeName]').type('エラー太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('1111111')
        cy.get('[data-cy=useTermStart]').type(yesterday)
        cy.get('[data-cy=useTermEnd]').type(tomorrow)
        cy.get('[data-cy=cyCardNo2]').type('2222222')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録時に社員IDの重複でエラー')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('社員コードは既に他の社員に登録されています。')

    })

    it('社員情報の新規登録時にICカード番号の重複でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeId]').type('347111')
        cy.get('[data-cy=cyEmployeeName]').type('エラー太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('1234567890')
        cy.get('[data-cy=useTermStart]').type(yesterday)
        cy.get('[data-cy=useTermEnd]').type(tomorrow)
        cy.get('[data-cy=cyCardNo2]').type('0987654321')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録時にICカード番号の重複でエラー')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('ICカード番号（正）は既に他の社員に登録されています。')

    })

    it('社員情報の新規登録時に社員ID不正文字でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeId]').type('３４７')
        cy.get('[data-cy=cyEmployeeName]').type('エラー太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('160')
        cy.get('[data-cy=useTermStart]').type(yesterday)
        cy.get('[data-cy=useTermEnd]').type(tomorrow)
        cy.get('[data-cy=cyCardNo2]').type('33')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録時に社員ID不正文字でエラー')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('社員コードは半角英数字で入力してください。')

    })

    it('社員情報の新規登録時に社員ID空欄でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeName]').type('エラー太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('160')
        cy.get('[data-cy=useTermStart]').type(yesterday)
        cy.get('[data-cy=useTermEnd]').type(tomorrow)
        cy.get('[data-cy=cyCardNo2]').type('33')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録時に社員ID空欄でエラー')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('社員コードを入力してください。')

    })

    it('社員情報の新規登録時にICカード番号不正文字でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeId]').type('34711')
        cy.get('[data-cy=cyEmployeeName]').type('エラー太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('１５８')
        cy.get('[data-cy=useTermStart]').type(yesterday)
        cy.get('[data-cy=useTermEnd]').type(tomorrow)
        cy.get('[data-cy=cyCardNo2]').type('3455')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録時にICカード番号不正文字でエラー')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('ICカード番号（正）は半角英数字で入力してください。')

    })

    it('社員情報の新規登録時に試用期間不正形式でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=cyEmployeeKbn]').select('社員')
        cy.get('[data-cy=cyEmployeeId]').type('34711')
        cy.get('[data-cy=cyEmployeeName]').type('エラー太郎')
        cy.get('[data-cy=cyEmployeeName2]').type('テストセル')
        cy.get('[data-cy=cyCardNo]').type('158')
        cy.get('[data-cy=useTermStart]').type('April 8, 2020 03:24:00')
        cy.get('[data-cy=useTermEnd]').type('April 10, 2020 03:24:00')
        cy.get('[data-cy=cyCardNo2]').type('3455')
        cy.get('[data-cy=useTermStart2]').type(yesterday)
        cy.get('[data-cy=useTermEnd2]').type(tomorrow)
        cy.get('[data-cy=useImpossible]').click()
        cy.get('[data-cy=memoNote]').type('社員情報の新規登録時に試用期間不正形式でエラー')
        cy.get('[data-cy=employee-info-add-form]').submit()
        cy.contains('ICカード番号（正）開始日に存在しない日付が設定されています。')

    })

    it('CSVファイルのアップロードで正常に登録される', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('社員情報.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('CSVファイルのアップロードに成功しました。')
        cy.get('[data-cy=employee-infos]').click()
        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeId164]').should('have.html', '164')
        cy.get('[data-cy=employeeName164]').should('have.html', 'CSV太郎')

    })

    it('EXCELファイルのアップロードで正常に登録される', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=selectFormat]').select('EXCEL', {force: true})
        cy.uploadFile('社員情報.xlsx', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('EXCELファイルのアップロードに成功しました。')
        cy.get('[data-cy=employee-infos]').click()
        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeId157]').should('have.html', '157')
        cy.get('[data-cy=employeeName157]').should('have.html', 'えくせる太郎')

    })

    it('EXCEL、CSV以外のファイルの選択でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('example.json', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードできるファイルはCSVまたはEXCELファイルとなります。')

    })

    it('ファイル未選択でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('アップロードするファイルを選択してください。')

    })

    it('ファイル内の社員区分空欄でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('社員区分エラー.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('社員区分を入力してください。（1レコード目）')

    })
    //
    it('ファイル内の社員コード空欄でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('社員コードエラー.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('社員コードを入力してください。（1レコード目）')

    })

    // it('アップするファイルのエラーが発生', () => {

    //     cy.get('[data-cy=employeeAdd]').click()
    //     cy.uploadFile('社員情報.zip', '[data-cy=selectFile]')
    //     cy.get('[data-cy=uploadFile]').submit()
    //     cy.contains('エラーが発生致しました。')

    // })

    it('ファイル内の社員区分存在なしでエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('社員区分不正文字.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('社員区分が存在しません。（1レコード目）')

    })

    it('ファイル内の社員コード不正文字でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('社員番号不正文字.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('社員コードは半角英数字で入力してください。（1レコード目）')

    })

    it('ファイル内のICカード不正文字でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('ICカード不正文字.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('ICカード番号（正）は半角英数字で入力してください。（1レコード目）')

    })

    it('ファイル内の使用期間不正形式でエラー', () => {

        cy.get('[data-cy=employeeAdd]').click()
        cy.uploadFile('使用期間不正文字.csv', '[data-cy=selectFile]')
        cy.get('[data-cy=uploadFile]').submit()
        cy.contains('ICカード番号（正）開始日に存在しない日付が設定されています。（1レコード目）')

    })

    it('社員情報の修正が正常に行われる', () => {

        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeDetail347]').click()
        cy.get('[data-cy=memoNote').clear()
        cy.get('[data-cy=memoNote]').type('社員情報の修正が正常に行われる')
        cy.get('[data-cy=updateEmployee]').click()
        cy.contains('更新を行いました。')

    })

    it('社員情報の削除が正常に行われる', () => {

        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeDetail347]').click()
        cy.get('[data-cy=employeeDelete]').click()
        cy.contains('削除を行いました。')

    })

    it('社員情報の削除の取り消しが正常に行われる', () => {

        cy.get('[data-cy=employeeInfosSelectForm]').submit()
        cy.get('[data-cy=employeeDetail347]').click()
        cy.get('[data-cy=cancelDelete]').click()
        cy.contains('更新を行いました。')

    })

    after(() => {
        // テストで作ったデータが残ってたら削除(全テスト一斉実施の際後続の試験に影響を与えないようにするため)
        cy.task('queryDb', 'DELETE FROM employee_infos WHERE employee_id = 347')
        cy.task('queryDb', 'DELETE FROM employee_infos WHERE employee_id = 164')
        cy.task('queryDb', 'DELETE FROM employee_infos WHERE employee_id = 157')
    })
})
