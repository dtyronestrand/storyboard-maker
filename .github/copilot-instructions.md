# Copilot Instructions for storyboard-maker

## Project Overview

- **Frameworks:** Laravel (PHP backend), Inertia.js, Vue 3 (frontend, TypeScript), Vite, TailwindCSS
- **Purpose:** Storyboard/course builder with modules, items, rubrics, and Google Docs export
- **Key Domains:** Courses, Modules, ModuleItems, Rubrics, QuizQuestions, Users

## Architecture & Data Flow

- **Backend:**
    - RESTful resource controllers for `Course`, `Module`, `ModuleItem`, `Rubric` (see `routes/web.php`)
    - Models use Eloquent ORM, with array-cast fields for flexible data (e.g., `objectives`, `items`, `criteria`)
    - Google Docs integration via `StoryboardController` (see `/google/*` routes)
- **Frontend:**
    - Vue 3 SFCs in `resources/js/components/`
    - Inertia.js bridges Laravel and Vue
    - Rubric editing: see `Rubrics/Edit.vue` for dynamic table structure

## Developer Workflows

- **Dev server:** `npm run dev` (Vite + Laravel)
- **Full stack dev:** `composer run dev` (runs PHP server, queue, logs, Vite concurrently)
- **Build:** `npm run build`
- **Lint/Format:** `npm run lint`, `npm run format`, `npm run format:check`
- **Tests:**
    - PHP: `php artisan test` or `vendor/bin/pest`
    - JS: No dedicated JS test suite (as of this writing)

## Conventions & Patterns

- **Eloquent:** Use `$fillable` and `$casts` for mass assignment and array fields
- **Vue Props:** Use explicit prop types, prefer `defineProps` and `defineEmits` in `<script setup lang="ts">`
- **ModuleItem ordering:** Use `scopeOrdered` and `orderBy('position')` for item sorting
- **Rubric structure:** `performance_levels` and `criteria` are arrays, edited in dynamic tables
- **Auth:** All resource routes are protected by `auth` and `verified` middleware

## Integration Points

- **Google API:** Uses `google/apiclient` for Docs export (see `StoryboardController`)
- **Inertia:** All main views rendered via Inertia (see `routes/web.php`)
- **Vite:** Handles asset bundling and hot reload

## Key Files & Directories

- `app/Models/` — Eloquent models (Course, Module, ModuleItem, Rubric, etc.)
- `routes/web.php` — Main route definitions
- `resources/js/components/` — Vue SFCs (UI logic)
- `composer.json`, `package.json` — Scripts, dependencies

## Examples

- To add a new rubric field, update `Rubric` model `$fillable`/`$casts` and Vue components in `Rubrics/`
- To add a new module item type, update `ModuleItem` model and related Vue logic

---

If any conventions or workflows are unclear, please ask for clarification or examples from the codebase.
