## Xsh-Bet-Simulator

## Project Overview

**Xsh Bet Simulator** is an innovative application designed to fetch match data from an API, generate betting slips with
4 matches each, and simulate the betting process by wagering 1000 TL on each slip. The application calculates and
displays potential profit or loss, providing users with valuable insights into the betting world and allowing them to
test their betting strategies effectively.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

## Prerequisites

You need to have the following software installed on your system to run this project:

- PHP >= 7.4
- Composer
- SQLite
- Laravel >= 8.x

## Installation

1. Clone this repository

```bash
git clone https://github.com/xshmrz/xsh-bet-simulator.git
```

2. Navigate to the project directory

```bash
cd xsh-bet-simulator
```

3. Install the required packages

```bash
composer install
```

4. Create the .env file

```bash
cp .env.example .env
```

5. Generate the application key

```bash
php artisan key:generate
```

6. Create and configure the database

```bash
touch database/database.sqlite
```

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

7. Run the database migrations

```bash
php artisan migrate
```

8. Start the development server

```bash
php artisan serve
```

## License

This project is licensed under the MIT License

## Contact

For questions about this project : [xshmrz@gmail.com](mailto:xshmrz@gmail.com)
