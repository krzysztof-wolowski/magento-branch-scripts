# Magento Branch Scripts

Magento Branch Scripts is a small local-development tool for teams and developers who use feature branches in their Magento workflow.

It creates Magento bootstrap scripts named after the current Git branch, so branch-specific experiments and one-off tasks can stay isolated instead of being mixed into the main codebase.

The package can live in any directory inside the Magento repository.

## Configuration

Copy the config template:

```bash
cp ./magento-branch-scripts/config.example ./magento-branch-scripts/config
```

Available settings:

- `MAGENTO_ROOT` controls which Magento root the tool uses
- `INSTALL_PATH` controls where the global `bootstrap` command is installed
- `EDITOR_COMMAND` controls which editor command opens the generated script; leave it empty to skip opening the file
- `COMMON_HELPER_NAMESPACE` controls the shared PHP helper namespace prefix used by `autoload.php`
- `COMMON_HELPER_SRC_DIR` controls the shared PHP helper source directory relative to the package root
- `PROJECT_HELPER_NAMESPACE` controls the project-specific PHP helper namespace prefix used by `autoload.php`
- `PROJECT_HELPER_SRC_DIR` controls the project-specific PHP helper source directory relative to the package root

The values in `config.example` are defaults only. You can point both helper layers at any namespace and directory structure you want.

## Install

After creating your local config, run:

```bash
./magento-branch-scripts/install.sh
```

This gives you a `bootstrap` command for the project. The install location is controlled by `INSTALL_PATH`.

## Usage

From the Magento project root, run:

```bash
bootstrap
```

This gives you:

- a Magento bootstrap script for the current Git branch in the package `bin/` directory
- a quick place for branch-specific code and experiments
- optional editor opening through `EDITOR_COMMAND`

PHP helpers are split into two configurable layers:

- a shared helper namespace/directory
- a project-specific helper namespace/directory
