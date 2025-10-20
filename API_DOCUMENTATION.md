# 📖 API Documentation - RS Puri Asih Backend

Complete API documentation untuk Dashboard Monitoring Pasien RS Puri Asih.

---

## 🌐 Base URL

**Development:** `http://localhost:8001/api`  
**Production:** `https://api.rs-puri-asih.com/api`

---

## 🔑 Authentication

Saat ini API bersifat open (no authentication required).

**Optional:** Implement JWT/Sanctum untuk protected routes.

---

## 📊 Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Success message"
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human readable error message",
    "details": "Technical details (only in debug mode)"
  }
}
```

---

## 🔌 API Endpoints

### 1. Health Check

**Endpoint:** `GET /health`

**Description:** Check API status

**Response:**
```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2025-10-17T10:30:00.000000Z"
}
```

**cURL:**
```bash
curl http://localhost:8001/api/health
```

---

### 2. Version Info

**Endpoint:** `GET /version`

**Description:** Get API version and available endpoints

**Response:**
```json
{
  "success": true,
  "data": {
    "version": "1.0.0",
    "api_name": "RS Puri Asih API",
    "endpoints": {
      "health": "/api/health",
      "dashboard": {...},
      "poli": {...},
      "kunjungan": {...}
    }
  }
}
```

---

### 3. Dashboard Statistics

**Endpoint:** `GET /dashboard/statistics`

**Description:** Get today's statistics with comparison

**Response:**
```json
{
  "success": true,
  "data": {
    "today": {
      "total": 145,
      "date": "2025-10-17",
      "date_formatted": "17 October 2025"
    },
    "comparison": {
      "last_week_total": 128,
      "last_week_date": "2025-10-10",
      "difference": 17,
      "percentage_change": 13.28
    },
    "busiest_poli": {
      "id": 1,
      "name": "Poli Umum",
      "code": "PU",
      "count": 45
    },
    "visit_types": {
      "new_patients": 89,
      "control_patients": 56
    },
    "monthly": {
      "total": 3475,
      "month": "October 2025"
    }
  },
  "message": "Statistics retrieved successfully"
}
```

**cURL:**
```bash
curl http://localhost:8001/api/dashboard/statistics
```

---

### 4. Poli Comparison

**Endpoint:** `GET /dashboard/poli-comparison`

**Description:** Compare today vs last week per poli

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "poli_id": 1,
      "poli_name": "Poli Umum",
      "poli_code": "PU",
      "today": 45,
      "last_week": 38,
      "difference": 7,
      "percentage_change": 18.42
    },
    {
      "poli_id": 2,
      "poli_name": "Poli Gigi",
      "poli_code": "PG",
      "today": 23,
      "last_week": 25,
      "difference": -2,
      "percentage_change": -8.00
    }
    // ... more poli
  ],
  "message": "Poli comparison retrieved successfully"
}
```

**cURL:**
```bash
curl http://localhost:8001/api/dashboard/poli-comparison
```

---

### 5. Trend Data (7 Days)

**Endpoint:** `GET /dashboard/trend`

**Description:** Get 7-day trend data

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "date": "2025-10-11",
      "date_formatted": "11 Oct 2025",
      "day_name": "Jumat",
      "count": 142
    },
    {
      "date": "2025-10-12",
      "date_formatted": "12 Oct 2025",
      "day_name": "Sabtu",
      "count": 98
    }
    // ... 7 days total
  ],
  "message": "Trend data retrieved successfully"
}
```

**cURL:**
```bash
curl http://localhost:8001/api/dashboard/trend
```

---

### 6. Monthly Report

**Endpoint:** `GET /dashboard/monthly/{month?}/{year?}`

**Parameters:**
- `month` (optional): Month number (1-12), default: current month
- `year` (optional): Year (YYYY), default: current year

**Examples:**
- `/dashboard/monthly` - Current month
- `/dashboard/monthly/10` - October current year
- `/dashboard/monthly/10/2025` - October 2025

**Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "month": 10,
      "year": 2025,
      "month_name": "October 2025"
    },
    "summary": {
      "total_visits": 3475,
      "total_new_patients": 2145,
      "total_control_patients": 1330
    },
    "poli_details": [
      {
        "poli_id": 1,
        "poli_name": "Poli Umum",
        "poli_code": "PU",
        "total": 950,
        "new_patients": 580,
        "control_patients": 370
      }
      // ... more poli
    ]
  },
  "message": "Monthly report retrieved successfully"
}
```

**cURL:**
```bash
# Current month
curl http://localhost:8001/api/dashboard/monthly

# Specific month/year
curl http://localhost:8001/api/dashboard/monthly/10/2025
```

---

### 7. Poli List

**Endpoint:** `GET /poli`

**Description:** Get all active poli

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Poli Umum",
      "code": "PU",
      "is_active": true
    },
    {
      "id": 2,
      "name": "Poli Gigi",
      "code": "PG",
      "is_active": true
    }
    // ... more poli
  ],
  "message": "Poli list retrieved successfully"
}
```

**cURL:**
```bash
curl http://localhost:8001/api/poli
```

---

### 8. Poli Detail

**Endpoint:** `GET /poli/{id}`

**Parameters:**
- `id` (required): Poli ID

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Poli Umum",
    "code": "PU",
    "is_active": true,
    "created_at": "2025-10-01T00:00:00.000000Z",
    "updated_at": "2025-10-01T00:00:00.000000Z"
  },
  "message": "Poli retrieved successfully"
}
```

**Error (Not Found):**
```json
{
  "success": false,
  "error": {
    "code": "POLI_NOT_FOUND",
    "message": "Poli not found"
  }
}
```

**cURL:**
```bash
curl http://localhost:8001/api/poli/1
```

---

### 9. Kunjungan List

**Endpoint:** `GET /kunjungan`

**Query Parameters:**
- `per_page` (optional): Items per page, default: 15
- `date` (optional): Filter by date (YYYY-MM-DD)
- `poli_id` (optional): Filter by poli ID

**Examples:**
- `/kunjungan` - All kunjungan (paginated)
- `/kunjungan?date=2025-10-17` - Today's visits
- `/kunjungan?poli_id=1` - Poli Umum visits
- `/kunjungan?date=2025-10-17&poli_id=1&per_page=20`

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "poli_id": 1,
        "tanggal_kunjungan": "2025-10-17T08:30:00.000000Z",
        "jenis_kunjungan": "baru",
        "poli": {
          "id": 1,
          "nama_poli": "Poli Umum",
          "kode_poli": "PU"
        }
      }
      // ... more visits
    ],
    "first_page_url": "http://localhost:8001/api/kunjungan?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost:8001/api/kunjungan?page=5",
    "next_page_url": "http://localhost:8001/api/kunjungan?page=2",
    "path": "http://localhost:8001/api/kunjungan",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 75
  },
  "message": "Kunjungan list retrieved successfully"
}
```

**cURL:**
```bash
# All kunjungan
curl http://localhost:8001/api/kunjungan

# Filter by date
curl "http://localhost:8001/api/kunjungan?date=2025-10-17"

# Filter by poli
curl "http://localhost:8001/api/kunjungan?poli_id=1"

# Pagination
curl "http://localhost:8001/api/kunjungan?page=2&per_page=20"
```

---

### 10. Kunjungan Detail

**Endpoint:** `GET /kunjungan/{id}`

**Parameters:**
- `id` (required): Kunjungan ID

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "poli_id": 1,
    "tanggal_kunjungan": "2025-10-17T08:30:00.000000Z",
    "jenis_kunjungan": "baru",
    "created_at": "2025-10-17T08:30:00.000000Z",
    "updated_at": "2025-10-17T08:30:00.000000Z",
    "poli": {
      "id": 1,
      "nama_poli": "Poli Umum",
      "kode_poli": "PU",
      "is_active": true
    }
  },
  "message": "Kunjungan retrieved successfully"
}
```

**Error (Not Found):**
```json
{
  "success": false,
  "error": {
    "code": "KUNJUNGAN_NOT_FOUND",
    "message": "Kunjungan not found"
  }
}
```

**cURL:**
```bash
curl http://localhost:8001/api/kunjungan/1
```

---

## 🔒 Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `STATISTICS_ERROR` | 500 | Failed to retrieve statistics |
| `COMPARISON_ERROR` | 500 | Failed to retrieve poli comparison |
| `TREND_ERROR` | 500 | Failed to retrieve trend data |
| `MONTHLY_REPORT_ERROR` | 500 | Failed to retrieve monthly report |
| `POLI_LIST_ERROR` | 500 | Failed to retrieve poli list |
| `POLI_NOT_FOUND` | 404 | Poli not found |
| `POLI_ERROR` | 500 | Failed to retrieve poli |
| `KUNJUNGAN_LIST_ERROR` | 500 | Failed to retrieve kunjungan list |
| `KUNJUNGAN_NOT_FOUND` | 404 | Kunjungan not found |
| `KUNJUNGAN_ERROR` | 500 | Failed to retrieve kunjungan |

---

## 🧪 Testing with Postman

### Import Collection

1. Create new collection: "RS Puri Asih API"
2. Set base URL variable: `{{base_url}}` = `http://localhost:8001/api`
3. Add requests for each endpoint

### Sample Requests

```javascript
// Environment Variables
{
  "base_url": "http://localhost:8001/api",
  "poli_id": "1",
  "kunjungan_id": "1"
}

// Pre-request Script (for all requests)
pm.environment.set("timestamp", new Date().toISOString());

// Tests (for success responses)
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has success field", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.success).to.be.true;
});

pm.test("Response has data field", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data).to.exist;
});
```

---

## 📊 Rate Limiting

Default: 60 requests per minute per IP.

Configurable via `.env`:
```env
API_RATE_LIMIT=60
```

Response headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## 🌍 CORS

CORS is enabled by default for all origins in development.

For production, configure in `.env`:
```env
CORS_ALLOWED_ORIGINS=https://dashboard.rs-puri-asih.com,https://app.rs-puri-asih.com
```

---

## 💡 Integration Examples

### JavaScript (Fetch API)

```javascript
// Get dashboard statistics
async function getDashboardStats() {
  try {
    const response = await fetch('http://localhost:8001/api/dashboard/statistics');
    const data = await response.json();
    
    if (data.success) {
      console.log('Total Today:', data.data.today.total);
      console.log('Busiest Poli:', data.data.busiest_poli.name);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}
```

### Vue.js (Axios)

```javascript
import axios from 'axios';

const API_BASE = 'http://localhost:8001/api';

export default {
  data() {
    return {
      statistics: null,
      loading: false,
      error: null
    }
  },
  async mounted() {
    this.fetchStatistics();
  },
  methods: {
    async fetchStatistics() {
      this.loading = true;
      try {
        const response = await axios.get(`${API_BASE}/dashboard/statistics`);
        if (response.data.success) {
          this.statistics = response.data.data;
        }
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    }
  }
}
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8001/api/']);

try {
    $response = $client->get('dashboard/statistics');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        $totalToday = $data['data']['today']['total'];
        echo "Total Pasien Hari Ini: " . $totalToday;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Python (Requests)

```python
import requests

API_BASE = 'http://localhost:8001/api'

def get_dashboard_statistics():
    try:
        response = requests.get(f'{API_BASE}/dashboard/statistics')
        data = response.json()
        
        if data['success']:
            print(f"Total Today: {data['data']['today']['total']}")
            print(f"Busiest Poli: {data['data']['busiest_poli']['name']}")
    except Exception as e:
        print(f"Error: {e}")

get_dashboard_statistics()
```

---

## 📞 Support

For API issues or questions:
- **Email:** api-support@rs-puri-asih.com
- **Documentation:** See README.md
- **Deployment:** See DEPLOYMENT.md

---

**API Version:** 1.0.0  
**Last Updated:** 17 Oktober 2025  
**Status:** Production Ready

