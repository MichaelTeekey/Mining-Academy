// ...existing code...

## cURL examples (quick test snippets)

Set base URL and token variables (adjust values):
```bash
export BASE="https://mining.zomacdigital.co.zw/api/v1"
export TOKEN="your_token_here"   # set after login: the plain token string (without "Bearer ")
```

Authentication
```bash
# Register
curl -i -s -X POST "$BASE/register" \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password","password_confirmation":"password","account_type":"student"}'

# Login -> returns token
curl -i -s -X POST "$BASE/login" \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'

# Logout (use token from login)
curl -i -s -X POST "$BASE/logout" \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Profile
```bash
curl -i -s -X GET "$BASE/me" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Courses
```bash
# List
curl -i -s -X GET "$BASE/courses" -H "Accept: application/json"

# Get
curl -i -s -X GET "$BASE/courses/{course_id}" -H "Accept: application/json"

# Create (instructor)
curl -i -s -X POST "$BASE/courses" \
  -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer $TOKEN" \
  -d '{"title":"Intro","description":"desc","price":100,"is_free":false,"status":"published","instructor_id":"<id>"}'

# Update
curl -i -s -X PUT "$BASE/courses/{course_id}" \
  -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer $TOKEN" \
  -d '{"title":"New title"}'

# Delete
curl -i -s -X DELETE "$BASE/courses/{course_id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Course Versions
```bash
curl -i -s -X GET "$BASE/course-versions" -H "Accept: application/json"
curl -i -s -X GET "$BASE/course-versions/{id}" -H "Accept: application/json"
curl -i -s -X POST "$BASE/course-versions" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"course_id":"<id>","version_number":"v1.0","snapshot":"Initial"}'
curl -i -s -X PUT "$BASE/course-versions/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"version_number":"v1.1"}'
curl -i -s -X DELETE "$BASE/course-versions/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Course Runs
```bash
curl -i -s -X GET "$BASE/course-runs" -H "Accept: application/json"
curl -i -s -X GET "$BASE/course-runs/{id}" -H "Accept: application/json"
curl -i -s -X POST "$BASE/course-runs" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"course_id":"<course_id>","name":"Cohort","start_date":"2025-10-25","end_date":"2025-12-25"}'
curl -i -s -X PUT "$BASE/course-runs/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"name":"Updated"}'
curl -i -s -X DELETE "$BASE/course-runs/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
// filepath: /home/robot/Documents/pg/php/mining-academy-backend/apiDocs.md
// ...existing code...

## cURL examples (quick test snippets)

Set base URL and token variables (adjust values):
```bash
export BASE="https://mining.zomacdigital.co.zw/api/v1"
export TOKEN="your_token_here"   # set after login: the plain token string (without "Bearer ")
```

Authentication
```bash
# Register
curl -i -s -X POST "$BASE/register" \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password","password_confirmation":"password","account_type":"student"}'

# Login -> returns token
curl -i -s -X POST "$BASE/login" \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'

# Logout (use token from login)
curl -i -s -X POST "$BASE/logout" \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Profile
```bash
curl -i -s -X GET "$BASE/me" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Courses
```bash
# List
curl -i -s -X GET "$BASE/courses" -H "Accept: application/json"

# Get
curl -i -s -X GET "$BASE/courses/{course_id}" -H "Accept: application/json"

# Create (instructor)
curl -i -s -X POST "$BASE/courses" \
  -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer $TOKEN" \
  -d '{"title":"Intro","description":"desc","price":100,"is_free":false,"status":"published","instructor_id":"<id>"}'

# Update
curl -i -s -X PUT "$BASE/courses/{course_id}" \
  -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer $TOKEN" \
  -d '{"title":"New title"}'

# Delete
curl -i -s -X DELETE "$BASE/courses/{course_id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Course Versions
```bash
curl -i -s -X GET "$BASE/course-versions" -H "Accept: application/json"
curl -i -s -X GET "$BASE/course-versions/{id}" -H "Accept: application/json"
curl -i -s -X POST "$BASE/course-versions" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"course_id":"<id>","version_number":"v1.0","snapshot":"Initial"}'
curl -i -s -X PUT "$BASE/course-versions/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"version_number":"v1.1"}'
curl -i -s -X DELETE "$BASE/course-versions/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

Course Runs
```bash
curl -i -s -X GET "$BASE/course-runs" -H "Accept: application/json"
curl -i -s -X GET "$BASE/course-runs/{id}" -H "Accept: application/json"
curl -i -s -X POST "$BASE/course-runs" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"course_id":"<course_id>","name":"Cohort","start_date":"2025-10-25","end_date":"2025-12-25"}'
curl -i -s -X PUT "$BASE/course-runs/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"name":"Updated"}'
curl -i -s -X DELETE "$BASE/course-runs/{id}" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"