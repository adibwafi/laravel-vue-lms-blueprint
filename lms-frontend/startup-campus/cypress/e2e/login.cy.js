describe('Login Flow', () => {
    it('logs in successfully', () => {
      cy.visit('/');
      
      console.log('VUE_APP_API_BASE_URL', Cypress.env('VUE_APP_API_BASE_URL'))
      console.log('CYPRESS_BASE_URL', Cypress.env('CYPRESS_BASE_URL'))
      console.log('CYPRESS_USER_PASSWORD', Cypress.env('CYPRESS_USER_PASSWORD'))
      console.log('CYPRESS_USER_EMAIL', Cypress.env('CYPRESS_USER_EMAIL'))
  
      cy.get('input[placeholder="Ketik e-mailmu"]').type(Cypress.env('CYPRESS_USER_EMAIL')); 
      cy.get('input[placeholder="Ketik kata sandi"]').type(Cypress.env('CYPRESS_USER_PASSWORD'));
      cy.contains('a.btn.btn-blue.btn-block', 'Masuk').click();
  
      cy.url().should('include', '/home');
  
      // Add more assertions here to verify specific elements or content on the dashboard
    });
  });