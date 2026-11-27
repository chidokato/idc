# Graph Report - indochinerealestate_new  (2026-11-27)

## Corpus Check
- 546 files · ~1,560,737 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 244 file(s) not represented in the graph (top: .scss 108, .css 52, (none) 20)

## Summary
- 218 nodes · 342 edges · 21 communities (4 shown, 17 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 6 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2cac4d51`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- SharedDbConfig
- SsoAuthService
- Illuminate\Database\Schema\Blueprint
- Illuminate\Http\Request
- SharedDbConfig.php
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
1. `SharedDbConfig` - 18 edges
2. `SsoAuthService` - 18 edges
3. `SsoController` - 17 edges
4. `SharedDbConfigController` - 14 edges
5. `SsoSharedTableController` - 8 edges
6. `SsoSharedTable` - 8 edges
7. `SsoAppController` - 7 edges
8. `SsoApp` - 6 edges
9. `AppServiceProvider` - 4 edges
10. `CreateSsoProfilesTable` - 4 edges

## Surprising Connections (you probably didn't know these)
- `SsoController` --references--> `SsoAuthService`  [EXTRACTED]
  app/Http/Controllers/SsoController.php → app/Services/SsoAuthService.php

## Import Cycles
- None detected.

## Communities (21 total, 17 thin omitted)

### Community 2 - "SsoAuthService"
Cohesion: 0.12
Nodes (3): SsoController, {closure#15}(), SsoAuthService

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (9): {closure#1}(), {closure#1}(), CreateSsoProfilesTable, {closure#1}(), CreateSsoAppsTable, {closure#1}(), CreateSsoSharedTablesTable, {closure#1}() (+1 more)

### Community 8 - "create.blade.php"
Cohesion: 0.50
Nodes (3): admin.alert, admin.layout.main, admin.shared_db_configs._form

### Community 9 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): admin.alert, admin.layout.main, admin.shared_db_configs._form

## Knowledge Gaps
- **16 isolated node(s):** `admin.alert`, `admin.layout.main`, `admin.alert`, `admin.shared_db_configs._form`, `admin.layout.main` (+11 more)
  These have ≤1 connection - possible missing edges. (Counts symbols only; 108 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **17 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SsoController` connect `SsoAuthService` to `web.php`, `SsoAuthService.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.167) - this node is a cross-community bridge._
- **What connects `admin.alert`, `admin.layout.main`, `admin.alert` to the rest of the system?**
  _16 weakly-connected nodes found - possible missing edges._
- **Should `web.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05263157894736842 - nodes in this community are weakly interconnected._
- **Why does `SharedDbConfigController` connect `SharedDbConfig` to `web.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.134) - this node is a cross-community bridge._
- **Should `SharedDbConfig` be split into smaller, more focused modules?**
  _Cohesion score 0.12688172043010754 - nodes in this community are weakly interconnected._
- **Why does `SsoAuthService` connect `SsoAuthService` to `SsoAuthService.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.131) - this node is a cross-community bridge._
- **Should `SsoAuthService` be split into smaller, more focused modules?**
  _Cohesion score 0.12473118279569892 - nodes in this community are weakly interconnected._