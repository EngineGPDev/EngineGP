## English 🇬🇧

😔 **Due to architectural shortcomings and significant technical obsolescence, the development of EngineGP v4 is being suspended indefinitely.**

🚀 We are currently starting work on a **new panel**, with a new name and story.

🔒 Development will take place in a **closed format** and will become available to a limited group of individuals at the start of **alpha testing**.  
🌍 **Access for all users** will be opened at the **beta testing** stage.

---

### 🧾 About the Project:

- 📄 **License:** Apache 2.0  
- 🧩 **Usage Scope:** Personal use and API access if there's a need to provide servers to third parties  
- 🛠 **Game Server Launch Method:**
  - **Docker** (preferred) 🐳  
  - **Native\*** (secondary) 🖥  

> \* **Native** — launching game servers in a `tmux` session, with resource limits managed by `systemd` and the `cgroups` tool.

---

# 🚀 EngineGP

**EngineGP** is an open source game server control panel licensed under **Apache 2.0**.
Originally created in 2014 with PHP 5.5 and no framework, EngineGP is being actively updated to meet modern standards.

It allows managing personal game servers or organizing a full-fledged hosting platform.

---

### 📦 Version Support

| Version | Branch               | Status   |
| ------- | -------------------- | -------- |
| 4.x     | `main`               | snapshot |
| 4.x     | `pre-release/v4.0.0` | beta.4   |
<!--
| 4.x     | `release/v4.x.x`     | stable   |
| 3.x     | `develop/v3.x.x`     | LTS      |
-->

---

### ⚙️ Requirements

* **PHP:** 8.1 and above
* **PHP Extensions:**
  `php-common`, `php-cli`, `php-memcache`, `php-mysql`, `php-xml`,
  `php-mbstring`, `php-gd`, `php-imagick`, `php-zip`, `php-curl`, `php-gmp`, `php-gz2`
* **Database:** MySQL or MariaDB
* **Web Server:** Apache or Nginx

---

### 📚 Libraries and Dependencies

* **Composer** — for dependency management

---

### 🎮 Supported Games

* Counter-Strike: 1.6
* Counter-Strike: Source
* Counter-Strike: Source v34
* Counter-Strike: Global Offensive
* Counter-Strike: 2
* Criminal Russia Multiplayer
* San Andreas Multiplayer
* Multi Theft Auto
* Minecraft Java Edition
* RUST

---

### ⚡ Automatic Installation

📦 [Autoinstall GitHub Repository](https://github.com/EngineGPDev/Autoinstall)

```bash
apt -y update && apt -y upgrade
apt -y install git
git clone https://github.com/EngineGPDev/Autoinstall.git
chmod +x ./Autoinstall/install.sh
./Autoinstall/install.sh
```

---

### 📄 Contribution Guidelines

* 📘 [CONTRIBUTING.md](./CONTRIBUTING.md) — contributing guide
* 🎨 [CODE\_STYLE.md](./CODE_STYLE.md) — code style guide

---

### 📣 Official Sources

<!--
- [Website](https://www.enginegp.com)  
- [Documentation](https://docs.enginegp.com)
-->
* [Telegram Channel](https://t.me/enginegpdev)
* [VK Group](https://vk.com/enginegp)

---

## Русский язык 🇷🇺

😔 **В силу архитектурных упущений и сильного технического устаревания, разработка EngineGP v4 замораживается на неопределённый срок.**

🚀 В настоящее время, мы начинаем работу над **новой панелью**, с новым названием и историей.

🔒 Разработка будет происходить в **закрытом формате** и станет доступна **узкому кругу лиц** на старте **альфа-тестирования**.  
🌍 **Доступ для всех пользователей** будет открыт на стадии **бета-тестирования**.

---

### 🧾 Немного о проекте:

- 📄 **Лицензия:** Apache 2.0  
- 🧩 **Спектр использования:** Личное использование и доступ по API в случае, если есть необходимость в предоставлении серверов третьим лицам  
- 🛠 **Метод запуска игровых серверов:**
  - **Docker** (приоритетно) 🐳  
  - **Native\*** (второстепенно) 🖥  

> \* **Native** — запуск игровых серверов в сессии `tmux`, с ограничением ресурсов со стороны `systemd` и инструментом `cgroups`.

---

# 🚀 EngineGP

**EngineGP** — это современная open-source панель управления игровыми серверами под лицензией **Apache 2.0**.
Проект начался в 2014 году на PHP 5.5 без фреймворков и с тех пор активно обновляется в соответствии с новыми стандартами.

Он позволяет как управлять личными игровыми серверами, так и запускать полноценный игровой хостинг.

---

### 📦 Поддержка версий

| Версия | Ветка                | Статус     |
| ------ | -------------------- | ---------- |
| 4.x    | `main`               | snapshot   |
| 4.x    | `pre-release/v4.0.0` | beta.4     |
<!--
| 4.x    | `release/v4.x.x`     | стабильная |
| 3.x    | `develop/v3.x.x`     | LTS        |
-->

---

### ⚙️ Требования

* **PHP:** 8.1 и выше
* **PHP-расширения:**
  `php-common`, `php-cli`, `php-memcache`, `php-mysql`, `php-xml`,
  `php-mbstring`, `php-gd`, `php-imagick`, `php-zip`, `php-curl`, `php-gmp`, `php-gz2`
* **База данных:** MySQL или MariaDB
* **Веб-сервер:** Apache или Nginx

---

### 📚 Библиотеки и зависимости

* **Composer** — менеджер PHP-зависимостей

---

### 🎮 Поддерживаемые игры

* Counter-Strike: 1.6
* Counter-Strike: Source
* Counter-Strike: Source v34
* Counter-Strike: Global Offensive
* Counter-Strike: 2
* Criminal Russia Multiplayer
* San Andreas Multiplayer
* Multi Theft Auto
* Minecraft Java Edition
* RUST

---

### ⚡ Автоматическая установка

📦 [Репозиторий автоматической установки](https://github.com/EngineGPDev/Autoinstall)

```bash
apt -y update && apt -y upgrade
apt -y install git
git clone https://github.com/EngineGPDev/Autoinstall.git
chmod +x ./Autoinstall/install.sh
./Autoinstall/install.sh
```

---

### 📄 Участие в разработке

* 📘 [CONTRIBUTING.md](./CONTRIBUTING.md) — правила участия
* 🎨 [CODE\_STYLE.md](./CODE_STYLE.md) — соглашения по стилю кода

---

### 📣 Официальные источники

<!--
- [Сайт](https://www.enginegp.com)  
- [Документация](https://docs.enginegp.com)
-->
* [Telegram канал](https://t.me/enginegpdev)
* [Группа ВКонтакте](https://vk.com/enginegp)

---
