#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
SKIP_PULL=0
SKIP_COMPOSER=0
SKIP_ASSETS=0
SKIP_MIGRATIONS=0
RUN_TESTS=1
MAINTENANCE_ACTIVATED=0

usage() {
    cat <<'EOF'
Uso: ./deploy.sh [opciones]

  --skip-pull         No ejecutar git pull --ff-only
  --skip-composer     No instalar dependencias PHP
  --skip-assets       No instalar ni compilar recursos frontend
  --skip-migrations   No ejecutar migraciones
  --run-tests         Ejecutar las pruebas (comportamiento predeterminado)
  --skip-tests        No ejecutar las pruebas automatizadas
  --help              Mostrar esta ayuda
EOF
}

for option in "$@"; do
    case "$option" in
        --skip-pull) SKIP_PULL=1 ;;
        --skip-composer) SKIP_COMPOSER=1 ;;
        --skip-assets) SKIP_ASSETS=1 ;;
        --skip-migrations) SKIP_MIGRATIONS=1 ;;
        --run-tests) RUN_TESTS=1 ;;
        --skip-tests) RUN_TESTS=0 ;;
        --help) usage; exit 0 ;;
        *) echo "Opción desconocida: $option" >&2; usage; exit 2 ;;
    esac
done

run() {
    printf '\n> '
    printf '%q ' "$@"
    printf '\n'
    "$@"
}

cleanup() {
    if [[ "$MAINTENANCE_ACTIVATED" -eq 1 ]]; then
        if ! "$PHP_BIN" artisan up; then
            rm -f -- "$PROJECT_PATH/storage/framework/down"
        fi
    fi
}

trap cleanup EXIT
cd "$PROJECT_PATH"

if [[ ! -f artisan ]]; then
    echo "No se encontró artisan en $PROJECT_PATH" >&2
    exit 1
fi

if [[ ! -f storage/framework/down ]]; then
    run "$PHP_BIN" artisan down --retry=60 --refresh=15
    MAINTENANCE_ACTIVATED=1
fi

if [[ "$SKIP_PULL" -eq 0 ]]; then
    run git pull --ff-only
fi

if [[ "$SKIP_COMPOSER" -eq 0 ]]; then
    composer_arguments=(install --no-interaction --prefer-dist --optimize-autoloader)

    if [[ "$RUN_TESTS" -eq 0 ]]; then
        composer_arguments+=(--no-dev)
    fi

    run "$COMPOSER_BIN" "${composer_arguments[@]}"
fi

if [[ "$SKIP_ASSETS" -eq 0 ]]; then
    run npm ci
    run npm run build
fi

if [[ ! -f public/build/manifest.json ]]; then
    echo 'No existe public/build/manifest.json. No se puede publicar una interfaz sin compilar.' >&2
    exit 1
fi

if [[ "$RUN_TESTS" -eq 1 ]]; then
    run "$PHP_BIN" artisan test --compact

    if [[ "$SKIP_COMPOSER" -eq 0 ]]; then
        run "$COMPOSER_BIN" install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    fi
fi

deploy_arguments=(artisan app:deploy --force --no-maintenance)

if [[ "$SKIP_MIGRATIONS" -eq 1 ]]; then
    deploy_arguments+=(--no-migrate)
fi

run "$PHP_BIN" "${deploy_arguments[@]}"
MAINTENANCE_ACTIVATED=1
run "$PHP_BIN" artisan up
MAINTENANCE_ACTIVATED=0
printf '\nDespliegue completado correctamente.\n'
