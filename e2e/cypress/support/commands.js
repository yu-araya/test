// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

// 環境依存の変数などはcypress.jsonのenvとして記載する

/**
 * システムユーザーでログイン
 */
Cypress.Commands.add("sysLogin", () => {
    cy.visit("TouchPay.standard/administrators/login")
    cy.get('#AdministratorLoginName').type(Cypress.env('sysuser'))
    cy.get('#AdministratorPassword').type(Cypress.env('syspassword'))
    cy.get('#AdministratorLoginForm').submit()
})

/**
 * 喫食登録を行う
 */
Cypress.Commands.add("addFoodHistory", (employeeId, instrumentDivision, foodDivision, cardReceptDate, cardReceptTime, reason) => {
    // 喫食の新規登録へ移動
    cy.get('[data-cy=food-history-infos]').click()
    cy.get('[data-cy=create-food-history-info]').click()

    // フォームデータ初期化
    cy.get('[data-cy=employee-id]').clear()
    cy.get('[data-cy=card-recept-date]').clear()
    // date-pickerが邪魔をしてreasonが探せなくなったため
    cy.get('[data-handler=hide]').click()
    cy.get('[data-cy=card-recept-time]').clear()
    cy.get('[data-cy=reason]').clear()

    // typeは空文字を入れられないため、空なら入れないようにする
    if(employeeId){
        cy.get('[data-cy=employee-id]').type(employeeId)
    }
    cy.get('[data-cy=instrument-division]').select(instrumentDivision)
    cy.get('[data-cy=food-division]').select(foodDivision)
    if(cardReceptDate){
        cy.get('[data-cy=card-recept-date]').type(cardReceptDate)
        // date-pickerが邪魔をしてreasonが探せなくなったため
        cy.get('[data-handler=hide]').click()
    }
    if(cardReceptTime){
        cy.get('[data-cy=card-recept-time]').type(cardReceptTime)
    }
    cy.get('[data-cy=reason]').type(reason)
    cy.get('[data-cy=food-history-info-add-form]').submit()
})

/**
 * 喫食検索を行う
 */
Cypress.Commands.add("searchFoodHistory", (year, month, employeeId, employeeName) => {
    cy.get('[data-cy=food-history-infos]').click()

    // データクリア
    cy.get('[data-cy=food-history-infos-emplyee-id]').clear()
    cy.get('[data-cy=food-history-infos-emplyee-name]').clear()

    cy.get('#FoodHistoryInfoCardReceptTimeYear').select(year).wait(3000)
    cy.get('#FoodHistoryInfoCardReceptTimeMonth').select(month).wait(3000)
    if(employeeId){
        cy.get('[data-cy=food-history-infos-emplyee-id]').type(employeeId)
    }
    if(employeeName){
        cy.get('[data-cy=food-history-infos-emplyee-name]').type(employeeName)
    }
    cy.get('[data-cy=food-history-info-select-form]').submit().wait(3000)
})

/**
 * 検索年月が正しいかチェック(食堂精算、社員別食堂精算用)
 */
Cypress.Commands.add("dateCheck", (now) => {

    // 2年前〜1年後まで確認できること
    for (let addYear = -2; addYear <= 1; addYear++) {
        let year = String(now.add(addYear, 'years').format('YYYY'));
        cy.get('#FoodHistoryInfoCardReceptTimeYear').select(year).wait(3000)
        cy.get('#FoodHistoryInfoCardReceptTimeYear').should('have.value', year)
    }

    // 01〜12まで表示されている事
    for (let addMonth = 1; addMonth <= 12; addMonth++) {
        let month = ('0' + addMonth).slice(-2)
        cy.get('#FoodHistoryInfoCardReceptTimeMonth').select(month).wait(3000)
        cy.get('#FoodHistoryInfoCardReceptTimeMonth').should('have.value', month)
    }

})

/**
 * 検索年月が正しいかチェック
 */
Cypress.Commands.add("dateCheckTwoYearsLater", (now, selectorYear, selectorMonth) => {

    // 2年前〜2年後まで確認できること
    for (let addYear = -2; addYear <= 2; addYear++) {
        let year = String(now.add(addYear, 'years').format('YYYY'));
        cy.get(selectorYear).select(year).wait(3000)
        cy.get(selectorYear).should('have.value', year)
    }
    
    // 01〜12まで表示されている事
    for (let addMonth = 1; addMonth <= 12; addMonth++) {
        let month = ('0' + addMonth).slice(-2)
        cy.get(selectorMonth).select(month).wait(3000)
        cy.get(selectorMonth).should('have.value', month)
    }

})

/**
 * 事業所が正しいかチェック
 */
Cypress.Commands.add("baseCheck", () => {

    // 本社、工場の表示が確認できること
    cy.get('#FoodHistoryInfoBaseKbn')
    .select('本社').wait(3000)
    cy.get('#FoodHistoryInfoBaseKbn').should('have.value', '1')
    cy.get('#FoodHistoryInfoBaseKbn')
    .select('工場').wait(3000)
    cy.get('#FoodHistoryInfoBaseKbn').should('have.value', '2')

})

/**
 * 喫食管理と予約管理を削除する
 */
Cypress.Commands.add("resetdb", () => {
    cy.task('queryDb', 'DELETE FROM food_history_infos')
    cy.task('queryDb', 'DELETE FROM reservation_infos')
})

/**
 * アップロードするファイルの選択処理
 */
Cypress.Commands.add('uploadFile', (fileName, selector) => {

    cy.get(selector).then(subject => {
        cy.fixture(fileName, 'hex', {timeout: 180000}).then((fileHex) => {

            let encoded = [];
            for (let i = 0, len = fileHex.length; i < len; i += 2) {
                encoded.push(parseInt(fileHex.substr(i, 2), 16));
            };
            const dataTransfer = new DataTransfer()
            const referenceData = subject[0]
            const testFile = new File([new Uint8Array(encoded)], fileName, {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            })

            dataTransfer.items.add(testFile)
            referenceData.files = dataTransfer.files
        })
    })
})
