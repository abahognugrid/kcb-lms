<!-- ## TODOs
If there is no float, return a generic error messasge to not allow the
the customer proceed after selecting the loan product they would like
to get a loan from. -->

## Loan Management System (LMS) API Documentation

## Intro

This is a living document for the LMS API. It covers various modules such as Loans. Whenever changes are made to the APIs, this document should be updated accordingly.

## API requirememnts

All requests made to the API must include the following:

### Headers

- Content-Type: `application/json`
- X-PARTNER-CODE `required`
- Bearer Token

## Loans

The Loans module handles loan applications, repayments, and loan-related queries.

### Loan Application

Allows a customer to apply for a loan.

POST `loans/application`

BODY

```json
{
  "phone": "256774614935",
  "amount": "50000",
  "loan_product_code": "LP-671756536C3C9",
  "loan_product_term_code": "LPT1-671756536DB12",
  "loan_purpose": "Business expansion",
  "number_of_installments": 4,
  "frequency_of_installments": "Monthly"
}
```

- **phone**: User’s phone number.
- **amount**: Loan amount requested.
- **loan_product_code**: The code of the loan product.
- **loan_product_term_code**: The term code for the loan product.
- **loan_purpose**: The purpose of the loan (e.g., "Business expansion").
- **number_of_installments**: Number of installments for loan repayment.
- **frequency_of_installments**: Repayment frequency (e.g., "Monthly").

RESPONSE

```json
{
  "message": "Your loan request of UGX 50000 was initiated successfully. Please wait for SMS confirmation"
}
```

- **message**: Confirmation of the loan application.

### Loan Repayment

Allows customers to repay part or full loan amounts.

POST `loans/repayment`

BODY

```json
{
  "phone": "256774614935",
  "amount": "25000"
}
```

- **phone**: User’s phone number.
- **amount**: Repayment amount.

RESPONSE

```json
{
  "message": "Your payment of 25000 was initiated successfully."
}
```

- **message**: Confirmation of the loan repayment.

### Loan Balance

Retrieves the outstanding loan balance for the user.

GET `loans/balance`

QUERY PARAMS

```json
?phone=256774614935
```

- **phone**: User’s phone number.

RESPONSE

```json
{
  "balance": 30750
}
```

- **balance**: Outstanding loan balance.

### Loan Products

Lists available loan products and their terms.

GET `loans/products`

QUERY PARAMS

```json
?phone=256774614935
```

- **phone**: User’s phone number.

RESPONSE

```json

{
    "loan_products": [
        {
            "by": "TheOne",
            "name": "Mobile Loan",
            "code": "LP-671756536C3C9",
            "minimum_principal": 5000,
            "maximum_principal": 1000000,
            "fees": [
                {
                    "name": "Application Fee",
                    "description": "Fee decription ...",
                    "calculation_method": "Percentage",
                    "value": 1.5,
                    "applicable_on": "Principal",
                    "applicable_at": "Disbursement"
                }
            ],
            "terms": [
                {
                    "code": "LPT1-671756536D990",
                    "term": 12,
                    "interest_rate": 10,
                    "interest_calculation_method": "Declining Balance - Discounted",
                    "repayment_cycles": [
                        "Weekly",
                        "Bi-weekly"
                    ],
                    "has_advance_payment": 0,
                    "advance_calculation_method": null,
                    "advance_value": null,
                    "extend_loan_after_maturity": 0,
                    "interest_type_after_maturity": null,
                    "interest_value_after_maturity": null,
                    "interest_after_maturity_calculation_method": null,
                    "recurring_period_after_maturity_type": null,
                    "recurring_period_after_maturity_value": null,
                    "include_fees_after_maturity": null
                },
                {
                    "code": "LPT1-671756536DB12",
                    "term": 4,
                    "interest_rate": 10,
                    "interest_calculation_method": "Flat",
                    "repayment_cycles": [
                        "Daily",
                        "Monthly"
                    ],
                    "has_advance_payment": 0,
                    "advance_calculation_method": null,
                    "advance_value": null,
                    "extend_loan_after_maturity": 0,
                    "interest_type_after_maturity": null,
                    "interest_value_after_maturity": null,
                    "interest_after_maturity_calculation_method": null,
                    "recurring_period_after_maturity_type": null,
                    "recurring_period_after_maturity_value": null,
                    "include_fees_after_maturity": null
                }
            ]
        },
        {
            "by": "Aporo Coo Ventures",
            "name": "Mobile Loan",
            "code": "LP-671756536DD50",
            "minimum_principal": 5000,
            "maximum_principal": 1000000,
            "fees": [...],
            "terms": [...]
        },
        ...
    ]
}
```

loan_products: List of loan products available on the market.

### Elegibility check

Determines if the user is eligible for any loan products.

GET `loans/elegibility`

QUERY PARAMS

```json
?phone=256774614935
```

- **phone**: User’s phone number.

RESPONSE

```json
{
  "message": "You are not eligible for any loan at the moment."
}
```

- **message**: Eligibility status message.

### Loan Ledger/Payments/Statment

Displays the loan ledger, showing recent loan repayments and the current balance.

GET `loans/ledge`

QUERY PARAMS

```json
?phone=256774614935
```

- **phone**: User’s phone number.

RESPONSE

```json
{
  "ledger": [
    {
      "amount": "25000.0000",
      "payment_date": "22/Oct/2024",
      "loan_balance": "30750"
    }
  ]
}
```

- **ledger**: List of repayments, including the amount, payment date, and outstanding balance.

### Loan Schedule / Repayment Schedule

Displays the repayment schedule for a user’s loan.

GET `loans/schedule`

QUERY PARAMS

```json
?phone=256774614935
```

- **phone**: User’s phone number.

RESPONSE

```json
{
  "loan_schedules": [
    {
      "installment_number": 1,
      "payment_due_date": "22/Nov/2024",
      "amount": "13750.0000"
    },
    {
      "installment_number": 2,
      "payment_due_date": "22/Dec/2024",
      "amount": "13750.0000"
    },
    {
      "installment_number": 3,
      "payment_due_date": "22/Jan/2025",
      "amount": "13750.0000"
    },
    {
      "installment_number": 4,
      "payment_due_date": "22/Feb/2025",
      "amount": "13750.0000"
    }
  ]
}
```

- **loan_schedules**: List of upcoming installments, including installment number, due date, and amount.
