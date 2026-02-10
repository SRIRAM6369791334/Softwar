# Supermarket OS - API Guide [#104]

## API Versioning [#100]
The system uses URI-based versioning. Current stable version is `v1`.
- Endpoint Prefix: `/api/v1/`

## Authentication
All API requests require a valid session or Bearer token (if implemented). 
- Headers: `Accept: application/json`

## Common Endpoints
- `GET /api/v1/notifications/unread`: Returns pending alerts.
- `POST /api/v1/pos/checkout`: Process a transaction.
- `GET /api/v1/products/search`: Search products for POS.

## Error Handling
The API returns standard HTTP status codes:
- `200`: Success
- `401`: Unauthorized
- `403`: Forbidden
- `500`: System Error
