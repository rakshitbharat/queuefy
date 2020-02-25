# Take care of Queue from Cron Job it self.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rakshitbharat/queuefy.svg?style=flat-square)](https://packagist.org/packages/rakshitbharat/queuefy)
[![Build Status](https://img.shields.io/travis/rakshitbharat/queuefy/master.svg?style=flat-square)](https://travis-ci.org/rakshitbharat/queuefy)
[![Quality Score](https://img.shields.io/scrutinizer/g/rakshitbharat/queuefy.svg?style=flat-square)](https://scrutinizer-ci.com/g/rakshitbharat/queuefy)
[![Total Downloads](https://img.shields.io/packagist/dt/rakshitbharat/queuefy.svg?style=flat-square)](https://packagist.org/packages/rakshitbharat/queuefy)

Useful when you want to run Queue from Cron Job.

## Installation

You can install the package via composer:

```bash
composer require rakshitbharat/queuefy
```

## Usage

From console command single queue thread will be runned.

* Mostly usefull on shared server (hosting).
* When you dont have access to put supervisor config file.
* Usefull when you can't install supervisor on server.
* When you can put Cronjob but cant put supervisor config.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email rakshitbharatproject@gmail.com instead of using the issue tracker.

## Credits

- [Rakshit Patel](https://github.com/rakshitbharat)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
