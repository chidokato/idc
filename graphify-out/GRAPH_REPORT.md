# Graph Report - indochinerealestate_new  (2026-11-27)

## Corpus Check
- 546 files · ~1,560,895 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 244 file(s) not represented in the graph (top: .scss 108, .css 52, (none) 20)

## Summary
- 219 nodes · 352 edges · 20 communities (5 shown, 15 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 7 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `36d55717`
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
1. `SsoAuthService` - 19 edges
2. `SharedDbConfig` - 18 edges
3. `SsoController` - 17 edges
4. `SharedDbConfigController` - 14 edges
5. `SsoSharedTable` - 8 edges
6. `SsoSharedTableController` - 8 edges
7. `SsoAppController` - 7 edges
8. `SsoApp` - 6 edges
9. `CreateSsoProfilesTable` - 4 edges
10. `CreateSsoAppsTable` - 4 edges

## Surprising Connections (you probably didn't know these)
- `SsoController` --references--> `SsoAuthService`  [EXTRACTED]
  app/Http/Controllers/SsoController.php → app/Services/SsoAuthService.php

## Import Cycles
- None detected.

## Communities (20 total, 15 thin omitted)

### Community 2 - "SsoAuthService"
Cohesion: 0.17
Nodes (3): SsoSharedTable, {closure#15}(), SsoAuthService

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (9): {closure#1}(), {closure#1}(), CreateSsoProfilesTable, {closure#1}(), CreateSsoAppsTable, {closure#1}(), CreateSsoSharedTablesTable, {closure#1}() (+1 more)

### Community 5 - "Illuminate\Http\Request"
Cohesion: 0.12
Nodes (3): SsoAppController, SsoSharedTableController, SsoController

### Community 8 - "create.blade.php"
Cohesion: 0.50
Nodes (3): admin.alert, admin.layout.main, admin.shared_db_configs._form

### Community 9 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): admin.alert, admin.layout.main, admin.shared_db_configs._form

## Knowledge Gaps
- **16 isolated node(s):** `admin.alert`, `admin.layout.main`, `admin.alert`, `admin.layout.main`, `admin.alert` (+11 more)
  These have ≤1 connection - possible missing edges. (Counts symbols only; 108 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **15 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SsoController` connect `Illuminate\Http\Request` to `web.php`, `SsoAuthService`, `SsoAuthService.php`?**
  _High betweenness centrality (0.169) - this node is a cross-community bridge._
- **What connects `admin.alert`, `admin.layout.main`, `admin.alert` to the rest of the system?**
  _16 weakly-connected nodes found - possible missing edges._
- **Should `web.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05263157894736842 - nodes in this community are weakly interconnected._
- **Why does `SsoAuthService` connect `SsoAuthService` to `SsoAuthService.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.136) - this node is a cross-community bridge._
- **Should `SharedDbConfig` be split into smaller, more focused modules?**
  _Cohesion score 0.12688172043010754 - nodes in this community are weakly interconnected._
- **Why does `SharedDbConfigController` connect `SharedDbConfig` to `web.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.130) - this node is a cross-community bridge._
- **Should `Illuminate\Database\Schema\Blueprint` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._