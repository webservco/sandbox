# DDEV: Windows

## Options

- [Docker Desktop](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/#wsl2-docker-desktop-install-script)
- [Docker CE](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/#wsl2-docker-ce-inside-install-script)

This article uses the "Docker Desktop" approach.

---

## Uninstall Docker Toolbox if present

---

## Chocolatey

Windows PowerShell > Run as administrator

```sh
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072;
iex ((New-Object System.Net.WebClient).DownloadString('https://chocolatey.org/install.ps1'))
```

Close PowerShell.

---

## Windows: DDEV, mkcert

Windows PowerShell > Run as administrator

```sh
# Install DDEV and mkcert
choco install -y ddev mkcert

# Setup mkcert
mkcert -install

# Set WSL2 to use the Certificate Authority installed on the Windows side
$env:CAROOT="$(mkcert -CAROOT)"; setx CAROOT $env:CAROOT; If ($Env:WSLENV -notlike "*CAROOT/up:*") { $env:WSLENV="CAROOT/up:$env:WSLENV"; setx WSLENV $Env:WSLENV }
```

Close PowerShell.

---

## WSL2

Microsoft Store > Windows Subsystem for Linux > Get

Windows PowerShell > Run as administrator

```sh
# Install Ubuntu
wsl --install
```

Reboot (if required), will continue setup afterwards (create user and password).

Windows PowerShell > Run as administrator

```sh
# Use WSL2 as default
wsl --set-default-version 2
```

### Update

Windows Update > Advanced options > Receive updates for other Microsoft products when you update Windows

Manually: `wsl --update`

### Troubleshooting

If nothing happens when running `wsl --install` (help is diplayed):

- make sure "Windows Subsystem for Linux" is installed;
- if installed, try specifying distro: `wsl --install --distribution Ubuntu`

---

## Docker Desktop

Note:
"Docker Desktop is free for small businesses (fewer than 250 employees AND less than $10 million in annual revenue), personal use, education, and non-commercial open source projects. Otherwise, it requires a paid subscription for professional use. Paid subscriptions are also required for government entities."

Download: https://www.docker.com/products/docker-desktop/

Leave checked "Use WSL 2 instead of Hyper-V (recommended)".

### Enable WSL integration

Settings > Resources > WSL integration > Enable integration with additional distros > Ubuntu > Apply and restart

Restart computer.

### Troubleshooting

#### "Docker Desktop requires a newer WSL kernel version."

WSL is probably not installed.

---

## Verify

### Show distros

Windows PowerShell / Terminal

```sh
wsl -l -v
```

Should display 3, all on WSL2, Ubuntu as default. Example:

```
  NAME                   STATE           VERSION
* Ubuntu                 Running         2
  docker-desktop         Running         2
  docker-desktop-data    Running         2
```

### Check mkcert installed correctly

Ubuntu:

```sh
echo $CAROOT
# Should display path /mnt/c/Users/__USER__/AppData/Local/mkcert
```

### Check docker is running inside Ubuntu

```sh
docker ps
# CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
```

---

## Ubuntu: DDEV, mkcert

Ubuntu:

```sh
curl -fsSL https://pkg.ddev.com/apt/gpg.key | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/ddev.gpg > /dev/null
echo "deb [signed-by=/etc/apt/trusted.gpg.d/ddev.gpg] https://pkg.ddev.com/apt/ * *" | sudo tee /etc/apt/sources.list.d/ddev.list >/dev/null
sudo apt update && sudo apt install -y ddev
```

---

## DDEV verify

Make sure the same version is used in both Windows and Ubuntu

```sh
# Windows
ddev.exe version

# Ubuntu
ddev version
```

---

## Notes

- No need to setup PHP 8, Composer 2, etc in Ubuntu, they will be managed by ddev.

---

## External resources

- [Docker Installation](https://ddev.readthedocs.io/en/latest/users/install/docker-installation/#windows)
- [DDEV Installation](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/#windows)
