# Installing PHP on Ubuntu/Pop!_OS, macOS, and Arch (Pacman)

## Ubuntu / Pop!_OS

1. Update package lists:
   ``` bash
   sudo apt update
   ```
2. Install PHP:
   ``` bash
   sudo apt install php
   ```
3. Verify installation:
   ``` bash
   php -v 
   ```

## macOS (Using Homebrew)

1. Ensure Homebrew is installed:
   ``` bash
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```
2. Install PHP:
   ```bash
   brew install php
   ```
3. Verify installation:
   ``` bash
   php -v
   ```

## Arch Linux (Using Pacman)

1. Install PHP:
   ``` bash
   sudo pacman -S php
   ```
2. Verify installation:
   ```bash
   php -v
   ```

Now PHP is installed and ready to use on your system! 🚀
