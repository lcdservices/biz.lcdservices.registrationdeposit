# biz.lcdservices.registrationdeposit

This extension provides the ability to define a minimum deposit amount for price field options. When implemented, the frontend event registration form will include a deposit field where the user can enter a value other than the total due. Upon submission, the total minimum deposit from the price field values are totaled and compared against the value entered by the user. If the deposit amount is less than the total minimum deposit, a validation error is thrown. If it exceeds the minimum deposit, the contribution is created with the full amount due but the transaction is processed for the deposit amount. This leaves the contribution in a partially paid state and allows staff to record future payments against the balance due.

Note: This extension is currently only integrated with Authorize.net.
