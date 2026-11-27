# Graph Report - indochinerealestate_new  (2026-11-27)

## Corpus Check
- 546 files · ~1,561,207 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 244 file(s) not represented in the graph (top: .scss 108, .css 52, (none) 20)

## Summary
- 225 nodes · 375 edges · 20 communities (4 shown, 16 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 11 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b7a44505`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- SharedDbConfig
- SsoAuthService
- Illuminate\Database\Schema\Blueprint
- SsoAuthService.php
- Illuminate\Http\Request
- AppServiceProvider.php
- create.blade.php
- edit.blade.php
- shared_db_configs/index.blade.php
- sso_apps/index.blade.php
- sso_shared_tables/index.blade.php
- login/index.blade.php
- apps.blade.php
- login.blade.php
- profile.blade.php

## God Nodes (most connected - your core abstractions)
1. `SsoAuthService` - 22 edges
2. `SsoController` - 19 edges
3. `SharedDbConfig` - 18 edges
4. `SharedDbConfigController` - 14 edges
5. `SsoSharedTableController` - 8 edges
6. `SsoAppController` - 7 edges
7. `SsoSharedTable` - 6 edges
8. `SsoApp` - 5 edges
9. `CreateSsoProfilesTable` - 4 edges
10. `CreateSsoAppsTable` - 4 edges

## Surprising Connections (you probably didn't know these)
- `SsoController` --references--> `SsoAuthService`  [EXTRACTED]
  app/Http/Controllers/SsoController.php → app/Services/SsoAuthService.php

## Import Cycles
- None detected.

## Communities (20 total, 16 thin omitted)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (9): {closure#1}(), {closure#1}(), CreateSsoProfilesTable, {closure#1}(), CreateSsoAppsTable, {closure#1}(), CreateSsoSharedTablesTable, {closure#1}() (+1 more)

### Community 5 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (3): SsoAppController, SsoSharedTableController, SsoController

### Community 8 - "create.blade.php"
Cohesion: 0.50
Nodes (3): admin.alert, admin.layout.main, admin.shared_db_configs._form

### Community 9 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): admin.alert, admin.layout.main, admin.shared_db_configs._form

## Knowledge Gaps
- **16 isolated node(s):** `layout.header`, `layout.header`, `layout.header`, `admin.alert`, `admin.layout.main` (+11 more)
  These have ≤1 connection - possible missing edges. (Counts symbols only; 109 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **16 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SsoController` connect `Illuminate\Http\Request` to `web.php`, `SsoAuthService`, `SsoAuthService.php`?**
  _High betweenness centrality (0.166) - this node is a cross-community bridge._
- **What connects `layout.header`, `layout.header`, `layout.header` to the rest of the system?**
  _16 weakly-connected nodes found - possible missing edges._
- **Should `web.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05263157894736842 - nodes in this community are weakly interconnected._
- **Why does `SsoAuthService` connect `SsoAuthService` to `SsoAuthService.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Should `SharedDbConfig` be split into smaller, more focused modules?**
  _Cohesion score 0.11092436974789915 - nodes in this community are weakly interconnected._
- **Why does `SharedDbConfigController` connect `SharedDbConfig` to `web.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.122) - this node is a cross-community bridge._
- **Should `Illuminate\Database\Schema\Blueprint` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._