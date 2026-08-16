# 🩺 NexLab Backend REST API & Admin Dashboard

Production-ready REST API backend and Web Admin Dashboard for **NexLab**, a premier medical diagnostic & laboratory booking platform operating in Tripoli, Libya. Built with Laravel 13, PHP 8.4, and Laravel Sanctum authentication.

All prices are strictly in **Libyan Dinars (LYD)**.

---

## 📚 Scramble Interactive API Documentation

NexLab includes automatic, interactive OpenAPI / Swagger documentation powered by **Dedoc Scramble**:

🔗 **Interactive API Docs URL**: `http://nexlab-backend.test/docs/api`  
📄 **OpenAPI Specification JSON**: `http://nexlab-backend.test/docs/api.json`

---

## 💻 Admin Web Dashboard

NexLab comes with an interactive, modern **Admin Dashboard Single-Page Web App**:

🔗 **Admin Dashboard URL**: `http://nexlab-backend.test/admin`  
🔐 **Admin Login URL**: `http://nexlab-backend.test/admin/login`

### Admin Credentials
- **Email**: `admin@nexlab.ly`
- **Password**: `password`

---

## 🔑 Seeded Demo Credentials & Catalog IDs

### User Credentials
| Role | Email | Password | Details |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@nexlab.ly` | `password` | NexLab System Administrator |
| **Patient User** | `monder@example.com` | `password` | Monder (User ID: 1, Age: 34, Blood: O+) |

### Seeded Diagnostic Test IDs
| Test ID | Test Name | Category | Price (LYD) | Fasting | Package |
| :---: | :--- | :---: | :---: | :---: | :---: |
| **`t1`** | CBC Complete Blood Count | Blood | 45.00 LYD | No | No |
| **`t2`** | Lipid Profile Panel | Heart | 85.00 LYD | Yes | No |
| **`t3`** | Thyroid Panel T3/T4/TSH | Thyroid | 110.00 LYD | No | No |
| **`t4`** | Vitamin D & B12 Panel | Energy | 140.00 LYD | No | Yes |
| **`t9`** | Full Body Executive Checkup | General | 299.00 LYD | Yes | Yes |

### Seeded Partner Lab IDs
| Lab ID | Partner Lab Name | Address (Tripoli) | Phone | Home Collection |
| :---: | :--- | :--- | :--- | :---: |
| **`l1`** | Tripoli Central Diagnostic Lab | Omar Al-Mukhtar Street, Tripoli | +218 21 123 4567 | Yes |
| **`l2`** | Al-Afia Medical Center | Gurji Road, Tripoli | +218 21 987 6543 | Yes |

---

## 🚀 Quick Setup & Installation

```bash
# Install PHP dependencies
composer install

# Set up environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations and seed Tripoli lab & admin data
php artisan migrate:fresh --seed

# Create storage symlink for prescription uploads
php artisan storage:link
```

### Base API URL
```
http://nexlab-backend.test/api
```

---

## 📡 API Endpoint Reference

### 1. Patient & Admin Authentication

| Endpoint | Method | Auth Required | Role | Description |
| :--- | :---: | :---: | :---: | :--- |
| `/api/register` | `POST` | No | Patient | Register patient account |
| `/api/login` | `POST` | No | Patient | Patient login & token generation |
| `/api/admin/login` | `POST` | No | Admin | Admin login & token generation |
| `/api/user` | `GET` | **Yes** | Any | Get current user profile |
| `/api/logout` | `POST` | **Yes** | Any | Revoke active token |

---

### 2. Catalog (Public)

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/tests` | `GET` | No | List tests (optional: `?category=Heart`, `?is_package=true`, `?search=blood`) |
| `/api/labs` | `GET` | No | List Tripoli partner labs |

---

### 3. Patient Features

| Endpoint | Method | Auth Required | Description |
| :--- | :---: | :---: | :--- |
| `/api/bookings` | `GET` / `POST` | **Yes** | View user's bookings / Create new lab booking |
| `/api/bookings/{id}/cancel` | `POST` | **Yes** | Cancel booking |
| `/api/results` | `GET` | **Yes** | List user's lab test results and biomarkers |
| `/api/prescriptions/upload` | `POST` | **Yes** | Upload prescription image/PDF |
| `/api/family-members` | `GET` / `POST` / `DELETE` | **Yes** | Manage family member profiles |
| `/api/payment-methods` | `GET` / `POST` / `DELETE` | **Yes** | Manage Libyan payment gateways (Edfaaly, Mobi Cash, Sadad, etc.) |

---

### 4. Admin Management Endpoints

All admin endpoints require `Authorization: Bearer <admin_token>`.

| Endpoint | Method | Description |
| :--- | :---: | :--- |
| `/api/admin/dashboard/stats` | `GET` | Revenue in LYD, booking counts, patients & lab statistics |
| `/api/admin/bookings` | `GET` | List all patient bookings across the network |
| `/api/admin/bookings/{id}/status` | `PATCH` | Update booking status (`pending`, `completed`, `cancelled`) |
| `/api/admin/tests` | `POST` | Create new diagnostic test or package |
| `/api/admin/tests/{id}` | `PUT` / `DELETE` | Update / Delete diagnostic test |
| `/api/admin/labs` | `POST` | Add new Tripoli partner lab |
| `/api/admin/labs/{id}` | `PUT` / `DELETE` | Update / Delete partner lab |
| `/api/admin/results` | `POST` | Publish lab test result and biomarkers for patient |

---

## 📮 Testing with Postman

Import **[NexLab_API_Postman_Collection.json](file:///c:/Users/Lenovo/Herd/nexlab-backend/NexLab_API_Postman_Collection.json)** into Postman.
- Folder `1. Authentication` -> `Login (Patient Monder)` saves `{{token}}`.
- Folder `7. Admin Portal` -> `Admin Login` saves `{{admin_token}}`.

---

## 🧪 Testing & Formatting

```bash
# Run 14 Pest automated feature tests
php artisan test --compact

# Run Pint code formatter
vendor/bin/pint --format agent
```
