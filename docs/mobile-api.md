# AssanZameen Mobile API — v1

REST API for the Android app. Every screen of the website (buyer, agent, town owner and the owner's team) has an endpoint here. Admin work stays on the website.

- **Base URL:** `https://<your-domain>/api/v1` (local: `http://localhost/pms/api/v1` or your XAMPP vhost)
- **Format:** JSON. Uploads use `multipart/form-data`.
- **Dates:** `YYYY-MM-DD`. Timestamps: ISO-8601 with timezone (`2026-10-29T09:14:20+05:00`, Asia/Karachi).
- **Money:** plain numbers in PKR (`150000`, `52222.5`). Format them in the app.

## 1. Headers

| Header | Value | When |
|---|---|---|
| `Accept` | `application/json` | always |
| `Authorization` | `Bearer <token>` | every endpoint marked 🔒 |
| `Accept-Language` | `en` (default) or `ur` | optional; messages and validation errors come back in that language |

## 2. Response envelope

Success:
```json
{ "success": true, "message": "Payment received.", "data": { ... }, "meta": { ... } }
```
`message` and `meta` appear only when there is something to say.

Paginated lists put the page info in `meta.pagination`:
```json
"meta": { "pagination": { "current_page": 1, "last_page": 6, "per_page": 20, "total": 112, "has_more": true } }
```
Use `?page=2&per_page=30` (max 100). Some lists add extra keys in `meta` (counts, totals, summary).

Error:
```json
{ "success": false, "message": "The amount field must be at least 100. (and 2 more errors)",
  "errors": { "amount": ["The amount field must be at least 100."], "screenshot": ["The screenshot field is required."] } }
```

| Status | Meaning | What the app should do |
|---|---|---|
| 200 / 201 | OK / created | — |
| 401 | Missing, wrong or revoked token | Clear the saved token, go to login |
| 402 | Not enough wallet balance (unlocks) | Show top-up screen; `errors.required_amount[0]` is the fee |
| 403 | Role not allowed, team member lacks the permission, or account pending approval/blocked | Show `message`; hide that menu |
| 404 | Not found, or it belongs to someone else | Show `message` |
| 422 | Validation failed | Show `errors[field][0]` under each field |
| 429 | Too many requests | Wait and retry |
| 500 | Server error | Show `message` |

Rate limits: 120 requests/min per user; login/register 10/min per IP; top-up requests 10/min; unlocks 20/min.

## 3. Roles

`user.role` decides the app's home screen:

| role | Who | Areas |
|---|---|---|
| `user` | Buyer | Public listings, wallet, unlock contacts, saved properties |
| `agent` | Dealer / agent | + `agent/*` and `leads` |
| `town_owner` | Scheme owner **or** a team member (`is_team_member: true`) | + `owner/*` and `leads` |

Team members only get the sections in `user.permissions` (also `GET owner/me/permissions`). Sections: `dashboard, towns, plots, installments, recovery, customers, employees, expenses, leads` (+ `team`, owner only). Show only allowed menus; the server returns 403 for the rest anyway.

Agents and town owners must be approved by the admin before they can log in (403 with a message until then).

---

## 4. Auth

### POST `/auth/register`
| field | rules |
|---|---|
| name | required |
| email | required, unique |
| phone | required, max 20 |
| role | required: `user`, `agent`, `town_owner` |
| password, password_confirmation | required, min 8 |
| device_name | optional (e.g. "Samsung A54") |

201 → `data: { user, token, requires_approval }`. When `requires_approval` is `true`, `token` is `null`: show "waiting for admin approval".

### POST `/auth/login`
`email`, `password`, `device_name` (optional) → `data: { user, token }`. Save the token securely (EncryptedSharedPreferences). Wrong password → 422 on `email`.

### 🔒 GET `/auth/me` → `data.user`
```json
{ "id": 2, "name": "Royal City Developers", "email": "owner@town.com", "phone": "03001234567",
  "role": "town_owner", "is_approved": true, "is_team_member": false, "owner_id": null,
  "permissions": ["dashboard","towns","plots","installments","recovery","customers","employees","expenses","leads","team"],
  "wallet_balance": 0, "agency": null, "created_at": "2026-09-22T19:14:27+05:00" }
```
### 🔒 PUT `/auth/me` — `name`, `phone`, `email` (any of them) → updated `user`
### 🔒 PUT `/auth/password` — `current_password`, `password`, `password_confirmation`. Signs out all other devices.
### 🔒 POST `/auth/logout` — revokes this token
### 🔒 DELETE `/auth/tokens` — sign out from every device

---

## 5. Public (no login needed; send the token if you have one)

### GET `/app/settings`
App config: `currency`, `languages`, `fees` (`property_contact_unlock`, `plot_contact_unlock`, …), `property_types`, `property_purposes`, `property_statuses`, `plot_features` (`key`, `label`, `premium_percent`), `lead_statuses`, `lead_priorities`, `lead_sources`. Load once at startup and use these lists for dropdowns.

### GET `/cities` → `[{ id, name, ... }]`

### GET `/schemes` 📄 — `?search=&city_id=` → housing schemes (towns) with plot counts
### GET `/schemes/{id}` → one scheme with blocks and plot stats

### GET `/plots` 📄 — available plots in active schemes
Filters: `town_id, type, size, block, min_price, max_price`
```json
{ "id": 7, "town_id": 1, "town_name": "Al Makkah Town", "plot_number": "7", "block": "C",
  "type": "residential", "size": "5 Marla", "features": [], "feature_labels": [], "rate_per_marla": null,
  "total_price": 6000000, "down_payment": 500000, "total_installments": 36, "status": "available",
  "customer": null, "booked_at": null }
```

### GET `/properties` 📄 — active listings
Filters: `search, city_id, town_id, type, purpose, min_price, max_price, agent_id`; `sort=latest|price_asc|price_desc|popular` (featured listings always come first)
```json
{ "id": 4, "title": "5 Marla House", "property_type": "house", "purpose": "for_sale", "price": 150000,
  "area_size": "6", "city": "Zahirpir", "city_id": 77, "town": null, "location": "zpr",
  "google_map_url": "https://maps.app.goo.gl/...", "description": "", "status": "available", "is_featured": false,
  "images": ["https://<domain>/public/property_images/....png"], "cover_image": "https://...png",
  "views_count": 0, "web_url": "https://<domain>/properties/4",
  "agent": { "id": 3, "name": "Al-Makkah Estate Agent", "agency_name": null, "phone": null, "email": null },
  "contact_unlocked": false, "updated_at": "2026-10-13T21:56:09+05:00" }
```
`agent.phone` / `agent.email` are `null` until the buyer unlocks the contact (or is the owner / an admin).

### GET `/properties/{id}` → one property (counts a view). Logged-in users also get `is_saved`.

---

## 6. Buyer (any logged-in user)

| Method | Path | Notes |
|---|---|---|
| 🔒 GET | `/wallet` | `{ balance, pending_top_ups, recent: [5 transactions] }` (fees are in `/app/settings`) |
| 🔒 GET | `/wallet/transactions` 📄 | `?type=deposit|unlock_fee|adjustment`; each has `direction: credit|debit`, `status` |
| 🔒 GET | `/payment-methods` | bank / JazzCash / Easypaisa accounts to send money to |
| 🔒 POST | `/wallet/top-ups` | **multipart**: `amount` (100–500000), `payment_method_id`, `sender_account_title`, `sender_account_number`, `screenshot` (jpg/png/webp ≤5 MB). Status stays `pending` until the admin approves |
| 🔒 POST | `/properties/{id}/unlock` | Charges the fee once. → `{ property (with agent phone/email), charged, wallet_balance }`. Second call is free (`charged: 0`). 402 if balance is low |
| 🔒 POST | `/plots/{id}/unlock` | Same for a plot → `{ owner: { name, phone, email }, charged, wallet_balance }` |
| 🔒 GET | `/my/unlocks` | `{ properties: [...], contacts: [...] }` everything already unlocked |
| 🔒 GET | `/my/saved-properties` 📄 | favourites |
| 🔒 POST | `/properties/{id}/save` | toggles; → `{ is_saved: true|false }` |

Unlocking a property automatically creates a lead for its agent.

---

## 7. Leads CRM (agent, or town owner with `leads`)

| Method | Path | Body / query |
|---|---|---|
| 🔒 GET | `/leads` 📄 | `search, status, priority, source, followup=late|today|upcoming`. `meta.counts.by_status` for tabs |
| 🔒 POST | `/leads` | `client_name*`, `client_phone*`, `client_email`, `town_id`, `plot_id`, `status*`, `priority*` (`hot|warm|cold`), `source*` (`portal|whatsapp|call|walk_in|social_media|banner`), `budget`, `next_followup_at` (date-time), `notes` |
| 🔒 GET | `/leads/{id}` | + `whatsapp: { intro|followup|visit: { text, url } }` ready-made messages |
| 🔒 PUT | `/leads/{id}` | same fields as POST |
| 🔒 PATCH | `/leads/{id}/status` | `status`: `new, contacted, site_visit, negotiation, closed_won, closed_lost` |
| 🔒 POST | `/leads/{id}/snooze` | `days`: 1, 3, 7, 14 or 30 — moves the next follow-up |
| 🔒 DELETE | `/leads/{id}` | |

WhatsApp: open `url` with an `ACTION_VIEW` intent (`https://wa.me/...`).

---

## 8. Agent (`role = agent`)

### 🔒 GET `/agent/dashboard`
`leads` (totals, `funnel: [{status, count, value}]`), `followups` (late / today), `commission` (total, received, pending), `listings` (counts, views).

### Listings
| Method | Path | Notes |
|---|---|---|
| 🔒 GET | `/agent/properties` 📄 | own listings; `search, status, type, purpose`. Each has `moderation_status` (`null` = waiting for admin, `active`, `inactive`) |
| 🔒 POST | `/agent/properties` | **multipart**: `title*`, `property_type*` (`house, plot, apartment, shop, commercial, residential, agricultural`), `purpose*` (`for_sale|for_rent`), `price*`, `area_size*` (e.g. "5 Marla"), `city_id*`, `town_id`, `location*`, `google_map_url`, `description`, `status` (`available, under_offer, sold, rented`), `images[]` (jpg/png/webp ≤2 MB, max 15 total) |
| 🔒 GET | `/agent/properties/{id}` | |
| 🔒 POST | `/agent/properties/{id}` | update (POST because of file upload); new `images[]` are added |
| 🔒 DELETE | `/agent/properties/{id}/images/{index}` | remove one photo (0-based index in `images`) |
| 🔒 POST | `/agent/properties/{id}/refresh` | bump to the top as "recently updated" |
| 🔒 DELETE | `/agent/properties/{id}` | |

New listings go live after admin approval.

### Commission deals
| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/agent/deals` 📄 | `search, status`; `meta.totals` |
| 🔒 POST | `/agent/deals` | `deal_title*, client_name*, client_phone*, deal_date*, deal_value*, total_commission*, received_amount*, property_id, partner_name, partner_share, notes` |
| 🔒 PUT | `/agent/deals/{id}` | same |
| 🔒 DELETE | `/agent/deals/{id}` | |

`status` is computed: `pending`, `partial`, `paid`. Deals with money pending have `reminder_whatsapp_url`.

### Dealer exchange (demands)
| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/agent/demands` 📄 | open demands from all dealers; `search, type, city`; `is_mine` flag |
| 🔒 POST | `/agent/demands` | `title*, property_type*` (`house, plot, commercial, agricultural, apartment`), `city*, target_location*, area_size*, budget_min, budget_max, urgency*` (`urgent|standard|flexible`), `details` |
| 🔒 PUT | `/agent/demands/{id}` | own demand only |
| 🔒 POST | `/agent/demands/{id}/close` | |
| 🔒 GET | `/agent/demands/{id}/contact` | the dealer's phone + WhatsApp link |

---

## 9. Town owner (`role = town_owner`)

Everything is limited to the owner's own schemes. A team member works inside their owner's account; the section in brackets must be in their `permissions`.

### 🔒 GET `/owner/me/permissions`
`{ is_owner, sections: [...allowed], all_sections: { key: label } }` — build the menu from this.

### 🔒 GET `/owner/dashboard` [dashboard]
```json
{ "towns_count": 2, "plots": { "total": 40, "available": 12, "booked": 25, "sold": 3 },
  "today": { "collected": 0, "due": 0 },
  "month": { "collected": 250000, "pending_due": 400000, "expenses": 35000, "salaries": 60000, "net": 155000 },
  "receivable": 11391111.26, "overdue": { "amount": 52222, "installments": 1 },
  "upcoming_7_days": 0, "recent_payments": [ installment + plot_number ] }
```

### Schemes and plots
| Method | Path | Notes |
|---|---|---|
| 🔒 GET | `/owner/towns` | each with `plots_count, available_plots_count, booked_plots_count, sold_plots_count` |
| 🔒 GET | `/owner/towns/{id}` | + `blocks` |
| 🔒 GET | `/owner/towns/{id}/plots` 📄 [plots] | `status, block` (`__none` = no block), `type, search` (plot no. or customer name/phone). Each plot has `booking_id`; `meta.counts` = available/booked/sold |

### Booking [plots]
**POST `/owner/plots/{id}/book`** (plot must be available)

Customer: either `customer_id` (an existing customer) **or** `customer[name]*`, `customer[phone]*`, `customer[father_name]`, `customer[cnic]` (13 digits, dashes optional), `customer[address]`.

| field | rules |
|---|---|
| booking_date | required |
| discount | ≥ 0 |
| down_payment | required, ≤ price − discount |
| installments_count | 0–360 (at least 1 if anything is left after down payment) |
| interval_months | `1, 3, 6, 12` |
| first_due_date | ≥ booking_date |
| down_payment_received | boolean — mark the down payment as paid now |
| remarks | optional |

201 → booking:
```json
{ "id": 6, "plot_id": 7, "allotment_number": "AL-001-00006", "status": "active", "booking_date": "2026-10-29",
  "customer": { ... }, "total_price": 6000000, "discount": 0, "final_price": 6000000, "down_payment": 100000,
  "installments_count": 12, "installment_amount": 491666.67, "interval_months": 1, "first_due_date": "2026-11-29",
  "portal_url": "https://<domain>/my-plot/<token>", "allotment_letter_url": "https://<domain>/...",
  "cancelled_at": null, "refund_amount": 0 }
```

| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/owner/plots/{id}/booking` | active booking + `ledger` + `history` (bookings, transfers) |
| 🔒 POST | `/owner/plots/{id}/cancel` | `refund_amount*` (≤ paid), `refund_date`, `cancel_reason*` → plot becomes available |
| 🔒 POST | `/owner/plots/{id}/transfer` | new customer (same as booking), `transfer_date*`, `transfer_fee`, `remarks` |

`portal_url` is a no-login page for the buyer (installments + receipts) — share it on WhatsApp. `allotment_letter_url` needs a website login (open in browser).

### Installments [installments]
**GET `/owner/plots/{id}/installments`**
```json
{ "plot": {...}, "booking": {...}|null, "buyer": { "name": "...", "phone": "..." },
  "summary": { "total": 4000000, "paid": 111111.11, "remaining": 3888888.85, "overdue": 0, "overdue_count": 0,
               "paid_count": 1, "count": 36, "percent": 3, "next_due": { installment } },
  "late_fee_percent": 2,
  "portal": { "url": "...", "share_whatsapp_url": "https://wa.me/..." } | null,
  "installments": [ {
      "id": 1, "plot_id": 3, "booking_id": 6, "number": 1, "label": "Installment #1",
      "amount": 111111.11, "late_fee": 0, "due_date": "2026-10-22", "paid_date": "2026-09-22",
      "status": "paid",               // pending | overdue | paid  (overdue = unpaid and past due)
      "days_late": 0, "remarks": "Cash", "received_by": "Ali",
      "receipt_url": "...",           // website page, needs login
      "public_receipt_url": "...",    // opens without login — use this in the app / share it
      "suggested_late_fee": 0,
      "whatsapp_url": "https://wa.me/..."   // receipt message if paid, reminder if not
  } ] }
```
| Method | Path | Body |
|---|---|---|
| 🔒 POST | `/owner/installments/{id}/receive` | `paid_date*` (≤ today), `late_fee`, `remarks` → installment + `receipt_whatsapp_url`. The plot becomes `sold` when the last one is paid. 422 if already paid |
| 🔒 POST | `/owner/installments/{id}/undo` | reverse a payment (back to pending) |

### Recovery desk [recovery]
**GET `/owner/recovery`** — `tab=overdue|today|week|promises`, `town_id`
```json
{ "data": [ { "plot_id": 9, "plot_number": "1", "block": null, "town": "Furqan Garden",
    "customer": { "name": "Hamza Bilal", "phone": "0300-1234567" },
    "late_amount": 52222, "late_count": 1, "oldest_due": "2026-10-28", "days_late": 1,
    "today_amount": 0, "upcoming_amount": 0, "next_due": null,
    "promise_date": "2026-10-31", "promise_broken": false,
    "last_followup": { "channel": "whatsapp", "outcome": "promised", "note": null, "by": "Ali", "at": "..." },
    "reminded_today": true, "whatsapp_url": "https://wa.me/..." } ],
  "meta": { "summary": { "late_amount": 52222, "late_customers": 1, "today_amount": 0, "week_amount": 0, "collected_today": 0 } } }
```
| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/owner/plots/{id}/followups` | last 50 follow-ups |
| 🔒 POST | `/owner/plots/{id}/followups` | `channel*` (`call, whatsapp, visit, sms`), `outcome*` (`reached, promised, no_answer, refused, sent`), `promise_date` (required when `promised`, ≥ today), `note` |

Tip: after the user taps a WhatsApp reminder, post a follow-up with `channel=whatsapp, outcome=sent`.

### Customers [customers]
| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/owner/customers` 📄 | `search` (name, phone, CNIC); each with `active_plots`, `documents_count` |
| 🔒 POST | `/owner/customers` | `name*, phone*` (10–15 digits), `father_name, cnic` (`35202-1234567-1`), `address` |
| 🔒 GET | `/owner/customers/{id}` | + plots, bookings, `documents` (download links) |
| 🔒 PUT | `/owner/customers/{id}` | same fields |

### Expenses [expenses]
| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/owner/expenses` 📄 | `search, town_id, category, from, to`; `meta` has totals by category |
| 🔒 POST | `/owner/expenses` | **multipart**: `town_id*, title*, category*, amount*, expense_date*` (≤ today), `paid_to, details, receipt` (jpg/png/webp/pdf ≤5 MB) |
| 🔒 POST | `/owner/expenses/{id}` | update (multipart) |
| 🔒 DELETE | `/owner/expenses/{id}` | |

Categories: `Utilities, Maintenance, Construction, Marketing, Legal, Office, Transport, General`.

### Employees & salary [employees]
| Method | Path | Body |
|---|---|---|
| 🔒 GET | `/owner/employees` | `town_id`, `month=YYYY-MM`; each shows paid/unpaid for that month and advance balance |
| 🔒 POST | `/owner/employees` | `town_id*, name*, designation*, monthly_salary*, phone` |
| 🔒 PUT | `/owner/employees/{id}` | same + `status` (`active|inactive`) |
| 🔒 GET | `/owner/employees/{id}/payments` | last 100 payments |
| 🔒 POST | `/owner/employees/{id}/payments` | `type*` (`salary|advance`), `amount*`, `salary_month` (YYYY-MM, required for salary), `advance_deduction` (≤ advance balance), `payment_date*`, `remarks` |

---

## 10. Android notes

- **Retrofit + OkHttp:** add an interceptor that sets `Accept`, `Accept-Language` (from the app's language setting) and `Authorization`. On any 401, clear the token and open login.
- **Images:** all image and file URLs are absolute — load them directly (Coil/Glide).
- **Uploads:** `MultipartBody`; arrays as `images[]`; nested fields as `customer[name]`.
- **Booleans in multipart:** send `1` / `0`.
- **Lists:** stop paging when `meta.pagination.has_more` is `false`.
- **WhatsApp / portal / receipt links:** open with `Intent.ACTION_VIEW`.
- **Language:** send `Accept-Language: ur` and show messages as they come; the server already translated them. Use RTL layouts for Urdu.

## 11. Server setup (once, on the live server)

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
```
`composer install` adds Laravel Sanctum (API tokens); `migrate` creates the `personal_access_tokens` table. Existing data is not touched.
