#!/usr/bin/env bash
# ============================================================================
# setup-test-db.sh — Crea la base de datos de TESTING dedicada para tseyor.
#
#   web_tseyor_org_2024_testing  (esquema clonado de la DB de producción, SIN datos)
#
# USO:
#   sudo ./scripts/setup-test-db.sh
#
# Qué hace:
#   1. Crea (o recrea) la DB web_tseyor_org_2024_testing
#   2. Da permisos al usuario de la app (.env DB_USERNAME)
#   3. Clona el ESQUEMA (solo estructura, sin datos) desde web_tseyor_org_2024
#
# Es idempotente: se puede correr las veces que haga falta; la DB de testing
# se descarta y reconstruye desde cero cada vez.
#
# IMPORTANTE: necesita privilegios de root para CREATE DATABASE / GRANT.
# Se espera que el comando se ejecute con sudo (o como root).
# ============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

# Cargar credenciales de la app desde .env
DB_HOST=$(grep '^DB_HOST=' .env | cut -d= -f2-)
DB_PORT=$(grep '^DB_PORT=' .env | cut -d= -f2-)
DB_USER=$(grep '^DB_USERNAME=' .env | cut -d= -f2-)
DB_PASS=$(grep '^DB_PASSWORD=' .env | cut -d= -f2-)
DB_NAME=$(grep '^DB_DATABASE=' .env | cut -d= -f2-)
DB_TEST="${DB_NAME}_testing"

for var in DB_HOST DB_PORT DB_USER DB_PASS DB_NAME; do
  if [[ -z "${!var}" ]]; then
    echo "ERROR: falta $var en .env" >&2
    exit 1
  fi
done

# Verificar que tenemos privilegios (root)
if [[ "$(id -u)" -ne 0 ]] && ! sudo -n true 2>/dev/null; then
  echo "ERROR: se necesita sudo (o root) para crear la DB de testing." >&2
  echo "Ejecuta:  sudo ./scripts/setup-test-db.sh" >&2
  exit 1
fi

# Conexión root: usar socket Unix si el host es local (auth_socket de MySQL
# en Ubuntu solo acepta root por socket, NO por TCP 127.0.0.1)
MYSQL_ROOT=(mysql -u root)
if [[ "$DB_HOST" != "localhost" && "$DB_HOST" != "127.0.0.1" ]]; then
  MYSQL_ROOT=(mysql --host="$DB_HOST" --port="$DB_PORT" -u root)
fi
MYSQL_APP=(mysql --host="$DB_HOST" --port="$DB_PORT" -u "$DB_USER" -p"$DB_PASS")
DUMP_APP=(mysqldump --host="$DB_HOST" --port="$DB_PORT" -u "$DB_USER" -p"$DB_PASS" --no-data --skip-comments)

echo "==> [1/3] Recreando DB de testing: $DB_TEST"

echo "    -> DROP DATABASE IF EXISTS $DB_TEST"
sudo "${MYSQL_ROOT[@]}" -e "DROP DATABASE IF EXISTS \`$DB_TEST\`;" || { echo "    ✗ FALLO en DROP DATABASE" >&2; exit 1; }
echo "    -> CREATE DATABASE $DB_TEST"
sudo "${MYSQL_ROOT[@]}" -e "CREATE DATABASE \`$DB_TEST\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || { echo "    ✗ FALLO en CREATE DATABASE" >&2; exit 1; }
echo "    -> GRANT ALL ON $DB_TEST.* TO '$DB_USER'@'localhost'"
sudo "${MYSQL_ROOT[@]}" -e "GRANT ALL PRIVILEGES ON \`$DB_TEST\`.* TO '$DB_USER'@'localhost';" || { echo "    ✗ FALLO en GRANT" >&2; exit 1; }
echo "    -> FLUSH PRIVILEGES"
sudo "${MYSQL_ROOT[@]}" -e "FLUSH PRIVILEGES;" || { echo "    ✗ FALLO en FLUSH" >&2; exit 1; }

echo "==> [2/3] Clonando esquema desde $DB_NAME (solo estructura, sin datos)"
echo "    -> mysqldump --no-data $DB_NAME | mysql $DB_TEST"
"${DUMP_APP[@]}" "$DB_NAME" | "${MYSQL_APP[@]}" "$DB_TEST" || { echo "    ✗ FALLO clonando esquema" >&2; exit 1; }

echo "==> [3/5] Sembrando datos base (users admin, grupo, equipo, permisos)"
echo "    -> INSERT user id=1 (admin)"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO users (id, name, email, password, email_verified_at, created_at, updated_at) VALUES (1, 'admin', 'admin@tseyor.org', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW(), NOW());" || { echo "    ✗ FALLO insertando user id=1" >&2; exit 1; }
echo "    -> INSERT user id=2"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO users (id, name, email, password, email_verified_at, created_at, updated_at) VALUES (2, 'usuario', 'usuario2@tseyor.org', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW(), NOW());" || { echo "    ✗ FALLO insertando user id=2" >&2; exit 1; }
echo "    -> INSERT grupo id=1"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO grupos (id, nombre, slug, created_at, updated_at) VALUES (1, 'Grupo Base', 'grupo-base', NOW(), NOW());" || { echo "    ✗ FALLO insertando grupo id=1" >&2; exit 1; }
echo "    -> INSERT equipo id=1 (con group_id=1)"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO equipos (id, nombre, slug, group_id, oculto, created_at, updated_at) VALUES (1, 'Equipo Test', 'equipo-test', 1, 0, NOW(), NOW());" || { echo "    ✗ FALLO insertando equipo id=1" >&2; exit 1; }
echo "    -> INSERT permisos Spatie (iguales a la DB real)"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO permissions (id, name, guard_name, created_at, updated_at) VALUES (1,'administrar contenidos','web',NOW(),NOW()),(2,'administrar equipos','web',NOW(),NOW()),(3,'administrar archivos','web',NOW(),NOW()),(4,'administrar usuarios','web',NOW(),NOW()),(5,'administrar social','web',NOW(),NOW()),(6,'administrar directorio','web',NOW(),NOW()),(7,'avanzado','web',NOW(),NOW()),(8,'administrar experiencias','web',NOW(),NOW()),(9,'administrar legal','web',NOW(),NOW()),(10,'coordinar equipo','web',NOW(),NOW());" || { echo "    ✗ FALLO insertando permissions" >&2; exit 1; }
echo "    -> INSERT rol superadministrador"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO roles (id, name, guard_name, created_at, updated_at) VALUES (1,'superadministrador','web',NOW(),NOW());" || { echo "    ✗ FALLO insertando rol superadmin" >&2; exit 1; }
echo "    -> Asignar todos los permisos al rol superadmin (role_has_permissions)"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO role_has_permissions (permission_id, role_id) VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1);" || { echo "    ✗ FALLO en role_has_permissions" >&2; exit 1; }
echo "    -> Asignar rol superadmin al user id=1 (model_has_roles)"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id) VALUES (1,'App\\\\Models\\\\User',1);" || { echo "    ✗ FALLO en model_has_roles" >&2; exit 1; }
echo "    -> Asignar todos los permisos directos al user id=1 (model_has_permissions)"
"${MYSQL_APP[@]}" "$DB_TEST" -e "INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id) VALUES (1,'App\\\\Models\\\\User',1),(2,'App\\\\Models\\\\User',1),(3,'App\\\\Models\\\\User',1),(4,'App\\\\Models\\\\User',1),(5,'App\\\\Models\\\\User',1),(6,'App\\\\Models\\\\User',1),(7,'App\\\\Models\\\\User',1),(8,'App\\\\Models\\\\User',1),(9,'App\\\\Models\\\\User',1),(10,'App\\\\Models\\\\User',1);" || { echo "    ✗ FALLO en model_has_permissions" >&2; exit 1; }

echo "==> [4/5] Resetear AUTO_INCREMENT (los tests asumen IDs fijos bajos)"
for t in users grupos equipos nodos; do
  echo "    -> ALTER TABLE $t AUTO_INCREMENT = 1"
  "${MYSQL_APP[@]}" "$DB_TEST" -e "ALTER TABLE \`$t\` AUTO_INCREMENT = 1;" || { echo "    ✗ FALLO reseteando AUTO_INCREMENT de $t" >&2; exit 1; }
done

echo "==> [5/5] Verificando"
TABLES=$("${MYSQL_APP[@]}" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_TEST';")
USERS=$("${MYSQL_APP[@]}" -N -e "SELECT COUNT(*) FROM $DB_TEST.users;")
GRUPOS=$("${MYSQL_APP[@]}" -N -e "SELECT COUNT(*) FROM $DB_TEST.grupos;")
EQUIPOS=$("${MYSQL_APP[@]}" -N -e "SELECT COUNT(*) FROM $DB_TEST.equipos;")
echo "    DB de testing lista: $DB_TEST con $TABLES tablas | $USERS users (admin id=1) | $GRUPOS grupos (id=1) | $EQUIPOS equipos (id=1)."
echo "OK"
