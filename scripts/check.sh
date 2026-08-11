#!/usr/bin/env bash
# Deterministic verification for tseyor (Laravel 12 + Inertia + Vue + Vite).
#
# Usage:
#   ./scripts/check.sh fast   # php -l + composer validate + vitest (seconds)
#   ./scripts/check.sh full   # fast + pint --test + phpunit + build + npm audit
#   ./scripts/check.sh        # same as full
#
# Exit code 0 = all gates green. Non-zero = at least one gate failed.
#
# KNOWN DEBT (2026-08-11, pre-existing, NOT introduced by this script):
#   - vendor/bin/pint --test : 457 files fail style checks (codebase not pint-clean)
#   - vendor/bin/phpunit     : 292 tests, 167 errors + 44 failures (many MCP tests
#                              fail with "null is not array" — likely env/server issue)
# These gates report FAIL until the debt is resolved. They run with --no-progress
# so output stays reviewable; run them individually for full output.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

MODE="${1:-full}"
FAILED=0

step() { printf '\n\033[1;36m== %s ==\033[0m\n' "$*"; }
ok()   { printf '\033[1;32mPASS\033[0m %s\n' "$*"; }
fail() { printf '\033[1;31mFAIL\033[0m %s\n' "$*"; FAILED=1; }

run() {
  local label="$1"; shift
  if "$@" >/dev/null 2>&1; then ok "$label"; else fail "$label"; fi
}

step "PHP syntax (php -l, app/ + tests/)"
PHP_LINT_FAIL=0
while IFS= read -r f; do
  if ! php -l "$f" >/dev/null 2>&1; then
    echo "  lint error: $f"
    PHP_LINT_FAIL=1
  fi
done < <(find app tests -name "*.php" -type f)
if [[ "$PHP_LINT_FAIL" -eq 0 ]]; then ok "php -l all files"; else fail "php -l"; fi

step "composer.json validation"
run "composer validate" composer validate --no-check-publish

step "Frontend unit tests (vitest)"
if npm test >/dev/null 2>&1; then ok "vitest"; else fail "vitest"; fi

if [[ "$MODE" == "fast" ]]; then
  printf '\n\033[1;32mFAST CHECKS DONE (exit %s)\033[0m\n' "$FAILED"
  exit "$FAILED"
fi

step "Code style (pint --test) — KNOWN DEBT: 457 files"
if timeout 120 vendor/bin/pint --test >/dev/null 2>&1; then
  ok "pint --test"
else
  fail "pint --test (pre-existing style debt)"
fi

step "PHP test suite (phpunit) — KNOWN DEBT: 167 errors + 44 failures"
if timeout 300 vendor/bin/phpunit --no-progress >/dev/null 2>&1; then
  ok "phpunit"
else
  fail "phpunit (pre-existing failures)"
fi

step "Production build (vite build)"
if timeout 180 npm run build >/dev/null 2>&1; then
  ok "npm run build"
else
  fail "npm run build"
fi

step "npm audit (better-npm-audit, needs network)"
if timeout 120 npm run audit >/dev/null 2>&1; then
  ok "npm audit"
else
  fail "npm audit"
fi

printf '\n\033[1;32mFULL CHECKS DONE (exit %s)\033[0m\n' "$FAILED"
exit "$FAILED"
