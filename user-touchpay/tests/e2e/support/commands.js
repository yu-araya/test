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
Cypress.Commands.add("login", (id, pass, waitApi = "loadReservationData") => {
    cy.visit("/#/login");
    cy.get("#loginIdInput").type(id);
    cy.get("#loginPasswordInput").type(pass);
    cy.get("#loginButton").click();
    if (waitApi === "loadReservationData") {
        cy.waitApi("POST", "/TouchPay.standard/api/loadReservationData", "render");
    } else if (waitApi) {
        cy.waitApi("GET", `/TouchPay.standard/api/${waitApi}`, "render");
    }
    cy.wait(2000);
});

const date = require("./date");
Cypress.Commands.add("openModal", (holiday = false) => {
    if (holiday) {
        cy.get(`#calendar-cell-${date.sunday}`).click({ force: true });
    } else {
        cy.get(`#calendar-cell-${date.wednesday}`).click({ force: true });
    }
});

Cypress.Commands.add("menuCountUp", (menuDivision, count) => {
    [...Array(count).keys()].forEach(() => {
        cy.get(
            `#reserve-modal-card${menuDivision} > .reserve-menu-card__count > .b-numberinput > :nth-child(3) > .button`
        ).click();
    });
});

Cypress.Commands.add("saveReservation", () => {
    cy.get("#reserve-modal-save").click();
    cy.get(".media-content > p")
        .should("exist")
        .contains("予約を登録します。よろしいですか？");
    cy.get(".modal-card-foot > .is-info").click();
    cy.waitApi("POST", "/TouchPay.standard/api/loadReservationData", "reload");
    cy.get(".media-content > p")
        .should("exist")
        .contains("予約を登録しました。");
    cy.get(".dialog > .modal-card > .modal-card-foot > .button").click();
    cy.get("#reserve-modal").should("not.exist");
});

Cypress.Commands.add("changePass", (password) => {
    cy.get("#change-password-input").type(password);
    cy.get("#password-confirm-input").type(password);
    cy.get("#change-password-button").click();
    cy.get(".modal-card-foot > .is-info").click();
    cy.wait(3000);
});

Cypress.Commands.add("waitApi", (method, path, alias) => {
    cy.server();
    cy.route(method, path).as(alias);
    cy.wait(`@${alias}`);
});
