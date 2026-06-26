# Invoice Code Sample

Hey, I've improved/adjusted this file using AI and highlighted just the things I felt are important on this kind of code assessment,
so I believe reading this file won't waste your time. There should be no AI slop or not much.



---

## Architecture

### Modular Monolith with Vertical Slices

Each module follows DDD layering — Domain, Infrastructure, and UseCases — but within UseCases, everything for a single operation lives together in one folder: the HTTP controller, command or query object, handler, and response DTOs. There are no generic `InvoiceService` or `NotificationService` classes; the directory tree itself communicates what the application does.

```
src/Modules/
├── Invoices/
│   ├── Domain/              # Entities, ValueObjects, Enums — pure PHP, no DI
│   ├── Infrastructure/      # Doctrine repository implementations
│   └── UseCases/
│       ├── CreateInvoice/   # Controller + Command + Handler
│       ├── SendInvoice/
│       ├── MarkInvoiceAsSent/
│       └── ViewInvoice/
├── Notifications/
│   ├── Api/                 # Public contract: interface, DTOs, events
│   ├── Facade/              # NotificationFacade (implements Api interface)
│   └── UseCases/
│       └── SendEmailNotification/
└── Shared/                  # Money, DomainLogicException, DomainEventsTrait
```

---

## Architectural Decisions

### Doctrine Attributes on Domain Entities

ORM mapping is written as PHP attributes directly on the entity classes. In production environment they should be placed in XML/YAML files to not make domain layer dirty with infrastructure concerns. 

### Transactional Outbox for Invoice Sending

The core data-consistency challenge in `SendInvoice` is the dual-write problem: persisting the invoice status change to the database and dispatching the email notification are two separate side effects. If the application crashes between them, the state becomes inconsistent — either the invoice is marked "Sending" with no email ever sent, or an email goes out without the status being updated.

The solution is a transactional outbox: `SendInvoiceHandler` writes the invoice status update and inserts the `SendEmailNotificationCommand` into the Doctrine queue table inside **a single database transaction**. If the transaction rolls back, neither write happens. If it commits, both are guaranteed to be durable.

This achieves **at-least-once delivery**. In edge cases (e.g., the worker crashes after sending the email but before acknowledging the message), the email may be sent a second time. That is an acceptable trade-off — a duplicate email is better than a lost one. For exactly-once delivery in production, an idempotency key can be passed to providers like SendGrid.


---

## Invoice Sending Flow

The `SendInvoice` operation spans both the `Invoices` and `Notifications` modules and involves an asynchronous hand-off. The sequence below shows how the transactional outbox ensures consistency across the two phases.


**View the diagram in [Mermaid Live Editor](https://mermaid.live/edit#pako:eNqdVetO2zAUfhXLfwhSgV5pG2lIQFtRDcqlnZC2osqLT4NFY3e2U-gQf_cAe8Q9yY5zgYy007T8SOL4-871O84zDRQH6lMD32KQAfQECzWLppLgtWTaikAsmbTkdCEAH8yQs8nkKluWYePhmcOMQfKhXCkRwBmTfAG6DL2BpXLYDOeWRlil12XoaOCAI2XFXATMCiUHLGAcysjeiUNeKWNDDePr8zLi1gGuY4iB3Cr9sCmycX_0mkU_YmJR9Lw1n4vhccK6YPohy-nYoAn7ykg5aen2jo6wVj65uhxPyIFI8ebgWfCXA4N-c7SGwBIdfvXq9WqF1Jvu1mrtppvuwtiAqBVoV_tK78QnYyHDBbhSWM2kYYELm3hcBVYLCbPi10hwjO2RaSiYREMYnmuIT-ZC8pP1kHuCFxBuDyGJt_55_3RCsgzeIL2TvTzHrBrkS0-zub177yjBZPz9CKuXlI1jFt77PLUI7y1Rc5JwEnPk14-fJMO_tzwa-ES63q29pIXrHrOsaHOQJTEcjfs3ExKBwdqHoGfujYVgiLdRBKcqirCrpZo5W5-ueseTfp4Q1mdCjGU2NuQD2TFpoDspsdBnx0cDqTh8Uq9WyeXHfPOtx7f-W5B-oKSJI0DRrWUwy_tLPAOoS4aUpVYoKrOb27n9s2eDm8uLDTmn2LR_6O9vBdii0lYLb_XqZpXicG2XaaGgiHPiwEcaAwEXBFkJRuDJgpZs4RJcCZ5PY4HmhtEnXJgls8E96tWoWOPxBguBQQDvr9xp5rnCFaJ0rA3KTzs5_K8BSAN5nYBMqHclnymuNAV2-wikjMxgPgV2oopHc8HBv0jTzqyaBQm_rNBcPD3MF41sEQ_xWPAg1eMCeAi7tEJDLTj1rY6hQiPQ2ENc0mdnc0rtPUQwpT6-ckx5SqfyBTl4nn5WKsppWsXhPfXnbGFwFS85ajv7Ub1-1Rgp6FMVS0v9WrfaSqxQ_5k-4bpd2z-sNQ_r3W6jfdit12oVuqZ-q7vfadYbrWa31my0qu3OS4V-T_xW99u1Rqd92O40Os12p9rpvPwGjAdDww).**

---

## Running the Project

All commands run inside Docker. Start the stack first:

```bash
./start.sh
```

| Task | Command                                                                        |
|---|--------------------------------------------------------------------------------|
| Process async queue | `docker compose exec api php bin/console messenger:consume async_doctrine -vv` |

The app is served at `http://localhost:8000`. PostgreSQL is exposed on port `5432`.

---


## Postman collection

There is a collection with Create & Send Invoice in:
```
Invoice Sample.postman_collection.json
```
