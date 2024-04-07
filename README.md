
# Queuefy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rakshitbharat/queuefy.svg?style=flat-square)](https://packagist.org/packages/rakshitbharat/queuefy)
[![Quality Score](https://img.shields.io/scrutinizer/g/rakshitbharat/queuefy.svg?style=flat-square)](https://scrutinizer-ci.com/g/rakshitbharat/queuefy)
[![Total Downloads](https://img.shields.io/packagist/dt/rakshitbharat/queuefy.svg?style=flat-square)](https://packagist.org/packages/rakshitbharat/queuefy)

Queuefy is a versatile package designed to facilitate queue handling directly from cron jobs, ideal for environments with specific constraints. Whether you're on a shared server, lacking supervisor config file access, or unable to install supervisor, Queuefy offers a robust solution. It's perfect for those who can manage cron jobs but face restrictions with more traditional queue management approaches.

## Features

- **Simple Integration**: Easy to set up with just a few commands.
- **Versatile Usage**: Works on shared servers and environments without supervisor.
- **Custom Queue Support**: Offers the ability to add custom queue commands via environment variables.

## Installation

Install Queuefy with Composer to streamline queue management on your server:

```bash
composer require rakshitbharat/queuefy
```

## Usage

To use Queuefy, simply run the following command from your console. This will initiate a single queue thread:

```bash
php artisan queue:work --timeout=0
```

For custom queue commands, add your preferred command to your `.env` file like so:

```
QUEUE_COMMAND_AFTER_PHP_ARTISAN="your_custom_command"
```

This is especially useful for shared hosting environments, or when you're unable to use traditional supervisor configurations for queue management.

## Changelog

For a detailed history of changes and improvements, refer to the [CHANGELOG](CHANGELOG.md).

## Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**. Please see [CONTRIBUTING](CONTRIBUTING.md) for guidelines on how to get involved.

### Security

If you encounter any security issues, please send an email to rakshitbharatproject@gmail.com instead of using the public issue tracker.

## Credits

- [Rakshit Patel](https://github.com/rakshitbharat) - **Project Lead**
- [All Contributors](../../contributors) - **Special Thanks**

## License

Queuefy is open-sourced software licensed under the [MIT License](LICENSE.md). Feel free to explore, modify, and distribute as you see fit.
