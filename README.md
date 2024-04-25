# Cashier CLI

This is a simple CLI application using `symfony/console` that emulates a basic shop or cashier system. It allows users to browse a catalog of products, add items to their cart, and apply discounts based on predefined rules.

> Note: This project is part of a technical challenge and not intended as a final product.

## Overview

The Cashier CLI provides a command-line interface for managing a virtual shop. Users can interact with the system to view products, add items to their cart, and apply discounts.

## Design Decisions

The application was intentionally designed with simplicity and flexibility in mind. Here are some key design decisions:

- **Flexibility in Discount Rules**: The `DiscountService` was designed to allow easy integration of new discount rules. By keeping the implementation of discount rules separate from the main application logic, we ensure that new rules can be added or modified without impacting the core functionality of the system.
- **Unit Testing Strategy**: Unit tests were implemented for both the `DiscountService` and individual discount rules. This ensures that each component of the discount system can be tested in isolation, allowing for easier debugging and maintenance.
- **Simplicity in Architecture**: The folder structure of the project was intentionally kept simple to avoid unnecessary complexity. While architectural patterns such as DDD, Hexagonal, or Onion could have been implemented, they were deemed excessive for the scope of this exercise. Instead, the focus was on delivering a functional solution with minimal overhead.
- **Command-Line Interface (CLI) Approach**: The decision to use a CLI interface was made to provide a familiar and lightweight user experience. By leveraging the `symfony/console` library, we were able to create a command-line application that is easy to use and navigate.
- **State Management**: Although a state management system could have been implemented to manage different screens and user interactions, such an approach was deemed unnecessary for the simplicity of this project. Instead, a basic flow control mechanism was used to handle user input and navigate between different screens.

Overall, the design of the Cashier CLI prioritizes simplicity, flexibility, and ease of use, making it a practical solution for simulating a basic shop or cashier system in a command-line environment.

## Requirements

- PHP 8.3.4 or above
- PHP Composer

### Installation

```sh
git clone https://github.com/rluders/cashier-cli.git
cd cashier-cli
composer install
```

### Usage

Ensure file permissions are set to execute for cashier.php, and execute it as follows:
```sh
./cashier.php
```

Or alternatively you can just execute it using:
```sh
php cashier.php
```

### Testing

At the project root directory execute:
```sh
./vendor/bin/pest
```

### Documentation

You can use [phpDocumentor](https://docs.phpdoc.org/3.0/) to generate the docs. 

```sh
wget https://phpdoc.org/phpDocumentor.phar
sudo chmod +x phpDocumentor.phar
./phpDocumentor run -d src -t docs
```

# License

This project is licensed under the MIT License.
