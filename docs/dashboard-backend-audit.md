# Dashboard/backend audit

Scope: dashboard/backend only. Public frontend/client-facing pages are intentionally out of scope.

## Real gaps to work point by point

- [ ] Agent/super-agent dashboard: `/dashboard` is currently admin-only, but the spec expects an agent home with metrics scoped by role/line.
- [ ] Raffle operational states: admin raffle logic uses `active/inactive`, but the required flow needs upcoming/active/finished semantics.
- [x] Raffle winners, option 1: each prize in the existing `prizes` JSON can now store winning number, detected user, line, and participation count.
- [ ] Raffle quick number assignment: current board supports selected numbers, but not automatic buttons for 1/5/10/20 numbers.
- [ ] Overview metrics: bonus and raffle metrics count states that do not match the actual modules.
- [ ] Line sales editing: sales exist in the `Ventas` module, but `Lineas` contains incomplete sales code and should be consolidated or cleaned.

## Covered enough in dashboard/backend, names may differ

- Clients: list/search, active/inactive, pause access, preferred line.
- Agents: create/edit, active/inactive, super-agent/agent roles, assigned lines, permissions.
- Lines: create/edit, cover/profile images, encargado, percentage, unlimited contact channels, platforms.
- Platforms: admin-only create/edit.
- Bonuses: create, date windows, line, quantity, per-user limits, active/upcoming/expired, assign to clients, mark used/claimed.
- Tickets: list, respond, change status, close/reopen.
- Dashboard notifications: bell/dropdown and notifications scoped to agent/admin.
- Sales: register by line/platform/date/amount and compute stats.

## Working rule

When evaluating implementation, judge by functional meaning, not literal names. Different labels are acceptable if the dashboard/backend behavior matches the required flow.
