# MarketLink project report structure

## 1. Introduction
Problem, users (customer, farmer, admin), and the pickup-only scope.

## 2. Requirements
Functional list from the SRS, plus constraints: no online payment, no delivery, farmer approval before listings, reviews only after a completed order, stock checks, and cutoff rules.

## 3. Architecture
Laravel 11 MVC, MySQL, Blade, Bootstrap 5, Leaflet, Chart.js. Role middleware. Services for orders and notifications.

## 4. Database
Sixteen domain tables plus settings. Foreign keys, indexes, and the order/item snapshot columns. Refer to `database/marketlink.sql`.

## 5. User flows
Registration, browse and filter, cart and pre-order, farmer accept/ready/complete, admin moderation and reports.

## 6. Security
Bcrypt passwords, CSRF, validation, role middleware, Eloquent queries, upload type and size limits, login throttling, Blade escaping, HTTPS-ready configuration.

## 7. Testing evidence
Screenshots of each role, the map, an order timeline, and a CSV export. Note the demo accounts.

## 8. Limitations
No card payments, no courier, chatbot is keyword matching, mail is logged unless SMTP is configured.

## 9. Conclusion
What the platform replaces at the market (paper lists and last-minute stock surprises) and what a later mobile client could use from `/api`.
