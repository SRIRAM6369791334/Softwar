# Supermarket OS - Deployment & Infrastructure Guide [#79, #101, #105]

This guide outlines the production deployment strategy for high-availability and horizontal scaling.

## 1. Load Balancing [#79]
To scale horizontally, use an Nginx or HAProxy load balancer in front of multiple application nodes.
- **Sticky Sessions**: Required as the system uses server-side sessions.
- **Health Checks**: Point to `/health` to ensure node availability.

## 2. Horizontal Scaling [#101]
- **Web Nodes**: Can be scaled behind the LB.
- **Shared Storage**: Use NFS or S3 for `./uploads/` and `./logs/`.
- **Session Store**: Use Redis instead of files for session sharing across nodes.

## 3. Database Replication [#105]
- **Master-Slave**: Use a Master for writes and a Slave for read-heavy reports.
- **Database.php**: Update to support separate read/write endpoints if needed.

## 4. Security & Compliance
- **SSL/TLS**: Mandatory for all production traffic.
- **Backup**: Daily backups are encrypted using `APP_KEY`.
- **Firewall**: Restrict DB access to application nodes only.
