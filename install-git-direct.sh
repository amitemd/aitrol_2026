#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_PATH="${PROJECT_PATH:-$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)}"
WEB_USER="${WEB_USER:-apache}"
PHP_BIN="${PHP_BIN:-php}"
PROJECT_OWNER="$(stat -c '%U' "$PROJECT_PATH")"
CURRENT_USER="$(id -un)"

if [[ ! "$PROJECT_PATH" =~ ^/[A-Za-z0-9._/-]+$ ]]; then
    echo "Ruta de proyecto no permitida: $PROJECT_PATH" >&2
    exit 1
fi

if [[ ! "$WEB_USER" =~ ^[A-Za-z_][A-Za-z0-9_-]*$ ]] || ! id "$WEB_USER" >/dev/null 2>&1; then
    echo "Usuario web no válido: $WEB_USER" >&2
    exit 1
fi

if [[ "$EUID" -ne 0 && "$CURRENT_USER" != "$PROJECT_OWNER" ]]; then
    echo "Ejecute este instalador como $PROJECT_OWNER, propietario del repositorio." >&2
    exit 1
fi

if [[ ! -d "$PROJECT_PATH/.git" ]] || [[ ! -f "$PROJECT_PATH/artisan" ]]; then
    echo "No existe un proyecto Laravel con Git en $PROJECT_PATH" >&2
    exit 1
fi

if [[ ! -f "$PROJECT_PATH/.env" ]]; then
    echo "No existe $PROJECT_PATH/.env" >&2
    exit 1
fi

if ! command -v setfacl >/dev/null 2>&1; then
    echo 'No se encontró setfacl. Instale el paquete acl antes de continuar.' >&2
    exit 1
fi

while IFS= read -r -d '' directory; do
    setfacl -m "u:$WEB_USER:rwx,d:u:$WEB_USER:rwx" "$directory"
done < <(find "$PROJECT_PATH" \
    -path "$PROJECT_PATH/vendor" -prune -o \
    -path "$PROJECT_PATH/node_modules" -prune -o \
    -type d -print0)

while IFS= read -r -d '' tracked_file; do
    setfacl -m "u:$WEB_USER:rw-" "$PROJECT_PATH/$tracked_file"
done < <(git -C "$PROJECT_PATH" ls-files -z)

setfacl -R -m "u:$WEB_USER:rwX" "$PROJECT_PATH/.git"

# La aplicación necesita leer la configuración, pero Git nunca debe modificarla.
setfacl -m "u:$WEB_USER:r--" "$PROJECT_PATH/.env"

upsert_env() {
    local key="$1"
    local value="$2"

    if grep -q "^${key}=" "$PROJECT_PATH/.env"; then
        sed -i "s|^${key}=.*|${key}=${value}|" "$PROJECT_PATH/.env"
    else
        printf '\n%s=%s\n' "$key" "$value" >>"$PROJECT_PATH/.env"
    fi
}

upsert_env 'DEPLOY_GIT_ENABLED' 'true'
upsert_env 'DEPLOY_GIT_HELPER' ''
upsert_env 'DEPLOY_GIT_USER' ''

cd -- "$PROJECT_PATH"
"$PHP_BIN" artisan optimize:clear --no-interaction

printf '\nAcceso directo de Git configurado correctamente.\n'
printf 'Usuario web: %s\nPropietario: %s\nProyecto: %s\n' "$WEB_USER" "$PROJECT_OWNER" "$PROJECT_PATH"
printf 'El panel ejecutará git pull --ff-only sin sudo.\n'
