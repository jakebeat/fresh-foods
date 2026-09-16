# Fresh Foods Cloud Platform

Cloud-hosted e-commerce platform built for a capstone business scenario: a food
distributor moving from intermediary-only sales to direct-to-customer ordering.

## Architecture
- **EC2 (t3.micro)** — PHP storefront served over HTTPS via nginx
- **RDS for MySQL** — customers, orders, and inventory, written inside transactions
- **S3** — product image storage
- **GitHub Actions → AWS Systems Manager** — CI/CD deploys with no SSH and no open port 22
- **CloudWatch** — dashboards and log groups for operational visibility

Every deployment is gated by an automated health check that confirms both the
application and database are reachable before a run is marked successful.

## Notable engineering details
- Inventory is decremented inside a database transaction with a row-level lock
  (`SELECT ... FOR UPDATE`) to prevent overselling under concurrent orders.
- All queries use prepared statements.
- Deployment credentials are never stored in the repo — GitHub Secrets and a
  server-side config file outside the codebase.

Built for IT473, Purdue University Global.
