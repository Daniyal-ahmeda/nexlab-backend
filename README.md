# 🩺 NexLab Backend REST API

Production-ready REST API backend for **NexLab**, a premier medical diagnostic & laboratory booking platform operating in Tripoli, Libya. Built with Laravel 13, PHP 8.4, and Laravel Sanctum authentication.

All prices are strictly in **Libyan Dinars (LYD)**.

---

## 🚀 Quick Setup & Installation

### Requirements
- **PHP**: ^8.4
- **Composer**: Installed
- **Laravel Herd** (or local PHP dev server)

### 1. Installation Commands
```bash
# Install PHP dependencies
composer install

# Set up environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations and seed Tripoli lab data
php artisan migrate:fresh --seed

# Create storage symlink for prescription uploads
php artisan storage:link
```

### 2. Local Environment Base URL
Running locally on **Laravel Herd**:
```
http://nexlab-backend.test/api
```

---

## 🔑 Seeded Demo Credentials

| Attribute | Value |
| :--- | :--- |
| **Email** | `monder@example.com` |
| **Password** | `password` |
| **Name** | Monder |
| **Age** | 34 |
| **Gender** | Male |
| **Blood Group** | O+ |

---

## 📡 API Endpoint Reference

### 1. Authentication
All protected routes require standard Sanctum header: `Authorization: Bearer <token>`.

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/register` | `POST` | No | Register new user account |
| `/api/login` | `POST` | No | Authenticate user & return Sanctum Bearer token |
| `/api/user` | `GET` | **Yes** | Fetch active user profile |
| `/api/logout` | `POST` | **Yes** | Revoke current Sanctum token |

---

### 2. Catalog (Tests & Partner Labs)

| Endpoint | Method | Auth Required | Query Parameters / Description |
| :--- | :---: | :---: | :--- |
| `/api/tests` | `GET` | No | List diagnostic tests. Optional filters: `?category=Heart`, `?is_package=true`, `?search=blood` |
| `/api/labs` | `GET` | No | List accredited Libyan partner labs in Tripoli |

---

### 3. Bookings

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/bookings` | `GET` | **Yes** | Get user's lab test bookings with test and lab details |
| `/api/bookings` | `POST` | **Yes** | Create new booking (auto-calculates total and generates `NX-XXXXX` ID) |
| `/api/bookings/{id}/cancel` | `POST` | **Yes** | Cancel existing booking |

---

### 4. Medical Records & Prescriptions

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/results` | `GET` | **Yes** | List user's lab test results and detailed biomarkers |
| `/api/prescriptions/upload` | `POST` | **Yes** | Upload doctor prescription file (`prescription`: PDF, PNG, JPG) |

---

### 5. Family Members

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/family-members` | `GET` | **Yes** | List user's registered family members |
| `/api/family-members` | `POST` | **Yes** | Register new family member |
| `/api/family-members/{id}` | `DELETE` | **Yes** | Remove family member by ID |

---

### 6. Libyan Payment Gateways

Supported Gateways: `Edfaaly`, `Mobi Cash`, `Sadad`, `Tyssir`, `Tadawul`, `Sahel`, `Moamalat`, `Cash`.

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/payment-methods` | `GET` | **Yes** | List user's payment methods |
| `/api/payment-methods` | `POST` | **Yes** | Add new payment method |
| `/api/payment-methods/{id}/default` | `POST` | **Yes** | Set payment method as default |
| `/api/payment-methods/{id}` | `DELETE` | **Yes** | Delete payment method |

---

## 📮 Testing with Postman

A pre-built Postman Collection is included in the project repository:

📄 **[NexLab_API_Postman_Collection.json](file:///c:/Users/Lenovo/Herd/nexlab-backend/NexLab_API_Postman_Collection.json)**

### How to use:
1. Open **Postman** -> Click **Import** -> Select `NexLab_API_Postman_Collection.json`.
2. Run **`1. Authentication / Login (Monder)`**.
3. The Postman test script automatically extracts the returned Sanctum Bearer token and sets `{{token}}` collection variable for all authenticated requests!

---

## 🧪 Testing & Code Formatting

### Run Automated Tests (Pest)
```bash
php artisan test --compact
```

### Run Code Formatter (Pint)
```bash
vendor/bin/pint --format agent
```
