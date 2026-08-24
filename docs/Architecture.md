# Architecture

## Product direction

Irani LMS is an independent WordPress LMS inspired by the capabilities and lessons of established LMS products such as LearnPress. It is not a LearnPress fork or translation.

## Core boundaries

### Learning
Courses, lessons, curriculum and learner progress.

### Enrollment
Access grants and enrollment lifecycle. Enrollment must not know gateway-specific details.

### Commerce
Products, orders, order items, prices and order state.

### Payment
Payment attempts, gateway abstraction, callbacks, verification and reconciliation.

### Assessment
Quizzes, questions, attempts, grading and exams.

### Certificate
Certificate templates, issuance and verification.

### API
Stable application contracts for WordPress frontends and future mobile clients.

## Payment flow

```text
Checkout
  -> Order created
  -> Payment created
  -> Gateway redirect
  -> Gateway callback
  -> Verify transaction
  -> Payment successful
  -> Order paid
  -> Enrollment granted
```

Payment verification and enrollment must be idempotent so a duplicated or delayed callback cannot create duplicate access or inconsistent order state.

## Localization

Persian localization is a product concern, not merely translation:

- RTL
- Persian validation and UI messages
- تومان as default display currency
- Jalali date presentation
- Persian number formatting where appropriate
- Iranian payment conventions

## Extension model

Gateway integrations and optional features must use stable interfaces so providers can be added without coupling the core domain to a specific company.
