<br/>
<p align="center">
  <!--<a href="https://github.com/EngineGPDev/EngineGP">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>-->

  <h3 align="center">EngineGP</h3>

  <p align="center">
    Game server control panel
    <br/>
    <br/>
    <a href="https://github.com/EngineGPDev/EngineGP"><strong>Explore the docs »</strong></a>
    <br/>
    <br/>
    <a href="https://github.com/EngineGPDev/EngineGP/issues">Report Bug</a>
    .
    <a href="https://github.com/EngineGPDev/EngineGP/issues">Request Feature</a>
  </p>
</p>

![Contributors](https://img.shields.io/github/contributors/EngineGPDev/EngineGP?color=dark-green) ![Forks](https://img.shields.io/github/forks/EngineGPDev/EngineGP?style=social) ![Stargazers](https://img.shields.io/github/stars/EngineGPDev/EngineGP?style=social) ![Issues](https://img.shields.io/github/issues/EngineGPDev/EngineGP) ![License](https://img.shields.io/github/license/EngineGPDev/EngineGP) 

## Table Of Contents

* [About the Project](#about-the-project)
* [Built With](#built-with)
* [Getting Started](#getting-started)
  * [Installation](#installation)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Authors](#authors)
* [Acknowledgements](#acknowledgements)

## About The Project

EngineGP is an open source control panel that provides the ability to manage and rent out game servers. EngineGP was written between 2010 and 2015 (Evaluated by coding style) by Valery Marchenko. The original EngineGP did not include licensing and was passed from hand to hand. In 2018, the author of the source code disappeared and the source code was picked up by the Open Source community and began to be promoted under the MIT license

## Built With

symfony
* polyfill

filp
* whoops

monolog
* monolog

xpaw
* php-source-query-class

szymach
* c-pchart

## Getting Started

Operating system: Debian 10 - 12, Ubuntu 20.04 - 22.04

php-fpm: 7.4 and higher

Webserver: Apache or NGINX

Database: MySQL or MariaDB

Composer

### Installation

1. Clone the repo

```sh
git clone https://github.com/EngineGPDev/EngineGP.git
```

2. Install composer packages

```sh
composer install
```

3. Import the enginegp.sql database from the root directory
In the panel table, specify the server IP address and password for the root user

4. In the system/data/config.php file, specify the basic settings
In the system/data/mysql.php file, specify the data from the database

## Roadmap

See the [open issues](https://github.com/EngineGPDev/EngineGP/issues) for a list of proposed features (and known issues).

## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.
* If you have suggestions for adding or removing projects, feel free to [open an issue](https://github.com/EngineGPDev/EngineGP/issues/new) to discuss it, or directly create a pull request after you edit the *README.md* file with necessary changes.
* Please make sure you check your spelling and grammar.
* Create individual PR for each suggestion.
* Please also read through the [Code Of Conduct](https://github.com/EngineGPDev/EngineGP/blob/main/CODE_OF_CONDUCT.md) before posting your first idea as well.

## License

Distributed under the MIT License. See [LICENSE](https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE.md) for more information.

## Authors

* **Solovev Sergei** - *web developer and system administrator* - [Solovev Sergei](https://github.com/SeAnSolovev) - *Design and development of the project 2018 - 2024*

## Acknowledgements

* [belomaxorka](https://github.com/belomaxorka)
