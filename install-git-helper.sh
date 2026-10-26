#!/usr/bin/env bash

set -Eeuo pipefail

if [[ "${EUID}" -ne 0 ]]; then
    echo 'Ejecute este instalador como root mediante sudo.' >&2
    exit 1
fi

PROJECT_PATH="${PROJECT_PATH:-$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)}"
WEB_USER="${WEB_USER:-apache}"
DEPLOY_USER="${DEPLOY_USER:-$(stat -c '%U' "$PROJECT_PATH")}"
DEPLOY_REMOTE="${DEPLOY_GIT_REMOTE:-origin}"
DEPLOY_BRANCH="${DEPLOY_GIT_BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
HELPER_PATH='/usr/local/sbin/aitrol-git-pull'
SUDOERS_PATH='/etc/sudoers.d/aitrol-git-pull'

if [[ ! "$PROJECT_PATH" =~ ^/[A-Za-z0-9._/-]+$ ]]; then
    echo "Ruta de proyecto no permitida: $PROJECT_PATH" >&2
    exit 1
fi

if [[ ! "$WEB_USER" =~ ^[A-Za-z_][A-Za-z0-9_-]*$ ]] || ! id "$WEB_USER" >/dev/null 2>&1; then
    echo "Usuario web no válido: $WEB_USER" >&2
    exit 1
fi

if [[ ! "$DEPLOY_USER" =~ ^[A-Za-z_][A-Za-z0-9_-]*$ ]] || ! id "$DEPLOY_USER" >/dev/null 2>&1; then
    echo "Usuario de despliegue no válido: $DEPLOY_USER" >&2
    exit 1
fi

if [[ ! "$DEPLOY_REMOTE" =~ ^[A-Za-z0-9._-]+$ ]] || [[ ! "$DEPLOY_BRANCH" =~ ^[A-Za-z0-9._/-]+$ ]]; then
    echo 'El remoto o la rama contienen caracteres no permitidos.' >&2
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

if ! command -v visudo >/dev/null 2>&1; then
    echo 'No se encontró visudo. Instale sudo antes de continuar.' >&2
    exit 1
fi

helper_tmp="$(mktemp)"
sudoers_tmp="$(mktemp)"
cleanup() {
    rm -f -- "$helper_tmp" "$sudoers_tmp"
}
trap cleanup EXIT

cat >"$helper_tmp" <<EOF
#!/usr/bin/env bash
set -Eeuo pipefail

PROJECT_PATH='$PROJECT_PATH'
REMOTE='$DEPLOY_REMOTE'
BRANCH='$DEPLOY_BRANCH'

cd -- "\$PROJECT_PATH"

case "\${1:-}" in
    --check)
        [[ "\$#" -eq 1 ]]
        [[ -w "\$PROJECT_PATH" && -w "\$PROJECT_PATH/.git" ]]
        git -c safe.directory="\$PROJECT_PATH" rev-parse --is-inside-work-tree | grep -qx true
        git -c safe.directory="\$PROJECT_PATH" remote get-url "\$REMOTE" >/dev/null
        ;;
    '')
        [[ "\$#" -eq 0 ]]
        exec git -c safe.directory="\$PROJECT_PATH" pull --ff-only "\$REMOTE" "\$BRANCH"
        ;;
    *)
        echo 'Argumento no permitido.' >&2
        exit 64
        ;;
esac
EOF

cat >"$sudoers_tmp" <<EOF
$WEB_USER ALL=($DEPLOY_USER) NOPASSWD: $HELPER_PATH, $HELPER_PATH --check
EOF

chmod 0440 "$sudoers_tmp"
visudo -cf "$sudoers_tmp"
install -o root -g root -m 0755 "$helper_tmp" "$HELPER_PATH"
install -o root -g root -m 0440 "$sudoers_tmp" "$SUDOERS_PATH"

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
upsert_env 'DEPLOY_GIT_HELPER' "$HELPER_PATH"
upsert_env 'DEPLOY_GIT_USER' "$DEPLOY_USER"

sudo -n -H -u "$WEB_USER" sudo -n -H -u "$DEPLOY_USER" "$HELPER_PATH" --check
cd -- "$PROJECT_PATH"
sudo -n -H -u "$WEB_USER" "$PHP_BIN" artisan optimize:clear --no-interaction

printf '\nAyudante Git instalado correctamente.\n'
printf 'Usuario web: %s\nUsuario de despliegue: %s\nProyecto: %s\n' "$WEB_USER" "$DEPLOY_USER" "$PROJECT_PATH"
