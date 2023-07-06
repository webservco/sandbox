# WSL

## Links

- [Basic commands for WSL](https://learn.microsoft.com/en-us/windows/wsl/basic-commands)

## Misc

```sh
# List local distributions
wsl --list

# List running distributions
wsl --list --running

# List distribution available to be installed
wsl --list --online

# Install specific distribution
wsl --install --distribution __NAME__

# Stop one distribution (it will start again automatically next time it is opened)
wsl --terminate Ubuntu

# Start one
wsl --distribution Ubuntu

# Stop all
wsl --shutdown
```

## Backup

- Stop

- Backup ext4.vhdx file from `C:\Users\__USER____\AppData\Local\Packages\CanonicalGroupLimited.UbuntuonWindows_79rhkp1fndgsc\LocalState\ext4.vhdx`

- Start

## Reset distribution

Settings > Apps (Apps & features) > Ubuntu > Advanced options > Reset > Reset

## Uninstall distribution

Windows PowerShell > Run as administrator

```sh
# List local distributions
wsl --list

# Remove distribution from WSL and delete all of the data associated with that Linux distribution
wsl --unregister __NAME__
```

Alternatively:

Settings > Apps (Apps & features) > Ubuntu > Uninstall

Repeat for any other distro.

## Uninstall WSL

Settings > Apps (Apps & features) > Windows Subsystem for Linux > Uninstall

## Disable WSL

Do not perform this step if only resetting and starting over.

Settings > Apps (Apps & features) > Optional features > More Windows features > Uncheck "Windows Subsystem for Linux"
