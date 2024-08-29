# Sports Bets Tracker

A PHP-based web application for tracking sports bets, specifically designed to manage and display parlays and their individual legs. This app allows you to create, view, update, and delete bets for each day, with options to mark legs as "Completed" or revert them back to "Pending."

![Screenshot of UI](images/Screenshot%20from%202024-08-28%2002-11-59.png)

## Features

- **Create a New Bet**: Add new bets with multiple legs, specifying odds, bet amount, potential payout, and conditions.
- **View Bets by Date**: Browse bets for a specific date, grouped by bet name.
- **Mark Legs as Completed**: Quickly mark legs as "Completed" with a single click, or revert them back to "Pending."
- **Delete Bets**: Remove entire bets from the system.
- **Responsive UI**: A clean and responsive UI with a dark theme for better readability.

## Prerequisites

- PHP 7.4 or higher
- MariaDB or MySQL
- Nginx or Apache
- PHP extensions: `pdo`, `pdo_mysql`

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/kprojects/sports_bets.git
   cd sports_bets
2. **Set up the database:**
   - Make sure you have MariaDB or MySQL installed and running.
   - Use the following SQL commands to create the database and table.
## Database Setup

Run the following SQL commands in your MariaDB/MySQL client to create the `sports_bets` database and `bets` table:

```sql
CREATE DATABASE sports_bets;
USE sports_bets;

CREATE TABLE bets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bet_id INT NOT NULL,
    date DATE NOT NULL,
    game_info VARCHAR(255) NOT NULL,
    parlay_name VARCHAR(255) NOT NULL,
    player_name VARCHAR(255) NOT NULL,
    team VARCHAR(10) NOT NULL,
    `condition` VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    odds DECIMAL(10,2) NOT NULL,
    bet_amount DECIMAL(10,2) NOT NULL,
    potential_payout DECIMAL(10,2) NOT NULL,
    parlay_status ENUM('Pending', 'Successful', 'Failed') DEFAULT 'Pending'
);
```
3. **Configure the database connection:**
   - Open `config.php` in a text editor.
   - Update the file with your MySQL/MariaDB credentials, including the database address and password.

4. **Deploy the application:**
   - Place the project folder in your web server's root directory.
   - Ensure that the web server has write permissions to the folder.
5. **Access the application:**
   - Open your web browser and navigate to `http://your-server-address/sports_bets`.

## Usage

- **To create a new bet**, fill in the form with details such as parlay name, game info, odds, bet amount, and potential payout. Add individual legs for each player with conditions and submit the form.
- **To update or mark legs as completed**, click the respective button next to each leg.
- **To delete a bet**, click the trash can icon next to the bet title.
## Contributing

Feel free to fork this repository, make changes, and submit pull requests. Your contributions are welcome!

## License

This project is open-source and available under the MIT License.
