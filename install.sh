#!/usr/bin/env bash

PACKAGE_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MAGENTO_ROOT="$PWD"
CONFIG_PATH="$PACKAGE_ROOT/config"

if [ -f "$CONFIG_PATH" ]; then
    . "$CONFIG_PATH"
fi

TARGET_PATH="${INSTALL_PATH:-$HOME/.local/bin/bootstrap}"
TARGET_DIR="$(dirname "$TARGET_PATH")"
PACKAGE_ROOT_RELATIVE="${PACKAGE_ROOT#"$MAGENTO_ROOT"/}"

if [ "$PACKAGE_ROOT_RELATIVE" = "$PACKAGE_ROOT" ]; then
    echo "Package root must be inside the configured Magento root: $MAGENTO_ROOT" >&2
    exit 1
fi

mkdir -p "$TARGET_DIR"

cat > "$TARGET_PATH" <<'EOF'
#!/usr/bin/env bash

MAGENTO_ROOT="$PWD"
PACKAGE_ROOT_RELATIVE="__PACKAGE_ROOT_RELATIVE__"
PACKAGE_ROOT="$MAGENTO_ROOT/$PACKAGE_ROOT_RELATIVE"
CONFIG_PATH="$PACKAGE_ROOT/config"

if [ -f "$CONFIG_PATH" ]; then
    . "$CONFIG_PATH"
fi

if [ "$PWD" != "$MAGENTO_ROOT" ]; then
    echo "Run 'bootstrap' from the configured Magento root: $MAGENTO_ROOT" >&2
    exit 1
fi

if [ ! -f "$MAGENTO_ROOT/app/bootstrap.php" ]; then
    echo "Magento bootstrap not found in: $MAGENTO_ROOT" >&2
    exit 1
fi

COMMAND_PATH="$PACKAGE_ROOT/bootstrap-command"

if [ ! -x "$COMMAND_PATH" ]; then
    echo "bootstrap-command not found in: $MAGENTO_ROOT" >&2
    exit 1
fi

exec "$COMMAND_PATH"
EOF

sed -i "s|__PACKAGE_ROOT_RELATIVE__|$PACKAGE_ROOT_RELATIVE|" "$TARGET_PATH"

chmod +x "$TARGET_PATH"

echo "Installed: $TARGET_PATH"
echo "Run 'bootstrap' from the Magento project root."
