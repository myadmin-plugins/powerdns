# MyAdmin PowerDNS Plugin

[![Build Status](https://github.com/detain/myadmin-powerdns/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-powerdns/actions)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-powerdns/version)](https://packagist.org/packages/detain/myadmin-powerdns)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-powerdns/downloads)](https://packagist.org/packages/detain/myadmin-powerdns)
[![License](https://poser.pugx.org/detain/myadmin-powerdns/license)](https://packagist.org/packages/detain/myadmin-powerdns)

PowerDNS DNS management plugin for the MyAdmin control panel. Provides domain hosting, DNS record management, and reverse DNS functionality through a PowerDNS backend.

## Features

- Domain creation and deletion with automatic SOA, NS, A, and MX record provisioning
- Full DNS record CRUD operations (A, AAAA, CNAME, MX, NS, TXT, SRV, PTR, and more)
- Basic and advanced DNS editor interfaces
- Reverse DNS management via SSH-based BIND zone updates
- Input validation for all DNS record types
- SOA serial number auto-increment on record changes
- Symfony EventDispatcher integration for hook-based plugin architecture
- API endpoint registration for programmatic DNS management

## Installation

```sh
composer require detain/myadmin-powerdns
```

## Requirements

- PHP >= 5.0
- Symfony EventDispatcher ^5.0
- PowerDNS backend database

## Testing

```sh
composer install
vendor/bin/phpunit
```

## License

Licensed under the LGPL-2.1. See [LICENSE](LICENSE) for details.
