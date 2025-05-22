
<h1 align="center">
  🚗 Laravel ChromeDriver Downloader
</h1>


<p align="center">
  <i>A Laravel Artisan command that auto-magically detects your Chrome version, downloads the right <b>ChromeDriver</b> for Windows 🪟, Linux 🐧, and Mac 🍏 — with exclusive WSL support!</i>
</p>

<p align="center">
  <marquee behavior="alternate" direction="right" scrollamount="5">
    🧠 Smart · 🤖 Automated · ⚡ Fast · 🧩 Cross-Platform · 💻 WSL-Aware
  </marquee>
</p>

---

<p align="center">
    <a href="https://packagist.org/packages/zgetro/laravel-dusk-chromedriver">
        <img src="https://poser.pugx.org/zgetro/laravel-dusk-chromedriver/v" alt="Latest Stable Version" />
    </a>
    <a href="https://packagist.org/packages/zgetro/laravel-dusk-chromedriver">
        <img src="https://poser.pugx.org/zgetro/laravel-dusk-chromedriver/downloads" alt="Total Downloads" />
    </a>
    <a href="https://packagist.org/packages/zgetro/laravel-dusk-chromedriver">
        <img src="https://poser.pugx.org/zgetro/laravel-dusk-chromedriver/v/unstable" alt="Latest Unstable Version" />
    </a>
    <a href="https://packagist.org/packages/zgetro/laravel-dusk-chromedriver">
        <img src="https://poser.pugx.org/zgetro/laravel-dusk-chromedriver/license" alt="License" />
    </a>
    <a href="https://packagist.org/packages/zgetro/laravel-dusk-chromedriver">
        <img src="https://poser.pugx.org/zgetro/laravel-dusk-chromedriver/require/php" alt="PHP Version Require" />
    </a>
</p>


### 🧩 Features

- 🎯 Detects your Chrome version precisely
- 💻 Supports downloading drivers for:
  - Windows (`chromedriver-win64`)
  - Linux (`chromedriver-linux64`)
  - macOS (`chromedriver-mac-arm64` / `mac-x64`)
- 🐧 **WSL Smart Detection** – detects Chrome version from Windows host
- ⚙️ Stores drivers in `.vendor/laravel/dusk/bin/`
- 🔀 Supports `--all` flag to download drivers for all OSes
- ✅ Laravel 6–11 compatible
- 🧪 PHPUnit support for testing the command

---

### 🚀 Installation

```bash
composer require zgetro/laravel-dusk-chromedriver --dev
```

### 🛠️ Usage

```bash
php artisan dusk:download-chromedriver
```
#### By default, this will:
- Detect the local Chrome version
- Download the matching ChromeDriver for your OS
- Save it as .vendor/laravel/dusk/bin/chromedriver-[os]


### 🧬 Download All Drivers
```bash
php artisan dusk:download-chromedriver --all
``` 
This downloads all supported platform drivers — ideal for testing or CI.

### ⚙️ Environment Compatibility

| OS      | Chrome Detection Method                               |
| ------- | ----------------------------------------------------- |
| Linux   | `google-chrome --version` / `chromium-browser`        |
| macOS   | `mdls` metadata or standard version queries           |
| Windows | PowerShell: read `.exe` version                       |
| **WSL** | **PowerShell bridge** to detect host Windows Chrome ✅ |

### 📦 Output Directory
All drivers are stored under:
```bash
.vendor/laravel/dusk/bin/
```
With friendly names:

- chromedriver-win
- chromedriver-linux
- chromedriver-mac

### 🧪 Testing
```bash
composer test
```
Supports phpunit via Orchestra Testbench.


### 🧙 Behind the Scenes
> Your Artisan command is your wand. This package just makes it magical.

The package:
- etches your Chrome version using OS-specific logic
- Queries official Chrome for Testing URLs
- Extracts & renames the ChromeDriver zip content
- Makes it executable where needed

### 🧰 Requirements
- PHP 7.4 or newer
- Laravel 6.x to 11.x
- Internet access (to download drivers)

### 🧾 License
MIT © zgetro

### 💡 Contributing
PRs welcome! Feel free to submit issues, add test coverage, or request new features.

<details> 
<summary>🐣 Fun Fact</summary>
Did you know the ChromeDriver you use every day is based on the WebDriver protocol – a W3C standard? You’re not just testing… you’re browsing the web like a boss.

</details>

<h6 align="center"> --Thanks CHATGPT-- </h6> 
