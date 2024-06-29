
# Xsh Bet Simulator

## Project Overview

**Xsh Bet Simulator** is an innovative application designed to fetch match data from an API, generate betting slips with
4 matches each, and simulate the betting process by wagering 1000 TL on each slip. The application calculates and
displays potential profit or loss, providing users with valuable insights into the betting world and allowing them to
test their betting strategies effectively.

## Live Demo

Experience the live version here: 

## Contact Information

For any inquiries or support, please contact us at: [xshmrz@gmail.com](mailto:xshmrz@gmail.com)

## Getting Started

To run this project locally, please follow the steps below.

### Prerequisites

Ensure you have the following installed on your local machine:

- Node.js
- npm (Node Package Manager)

### Installation

1. **Clone the Repository:**

   Begin by cloning the repository to your local machine using the following command:

    ```bash
    git clone https://github.com/xshmrz/xsh-bet-simulator.git
    cd xsh-bet-simulator
    ```

2. **Install Dependencies:**

   Navigate to the project directory and install the required dependencies:

    ```bash
    npm install
    ```

3. **Configure Environment Variables:**

   Create a `.env` file in the root directory of the project and add the following content to configure the necessary
   environment variables:

    ```plaintext
    REACT_APP_API_URL_MATCH_GET=https://sportsbook.iddaa.com/SportsBook/getPopulerBets?sportId=1&limit=40
    REACT_APP_API_URL_CHECK=https://statistics.iddaa.com/broadage/getEventListCache?SportId=1&SearchDate=

    REACT_APP_FIREBASE_API_KEY=your-firebase-api-key
    REACT_APP_FIREBASE_AUTH_DOMAIN=your-firebase-auth-domain
    REACT_APP_FIREBASE_DATABASE_URL=your-firebase-database-url
    REACT_APP_FIREBASE_PROJECT_ID=your-firebase-project-id
    REACT_APP_FIREBASE_STORAGE_BUCKET=your-firebase-storage-bucket
    REACT_APP_FIREBASE_MESSAGING_SENDER_ID=your-firebase-messaging-sender-id
    REACT_APP_FIREBASE_APP_ID=your-firebase-app-id

    REACT_APP_MATCHES_PER_COUPON=4
    ```

4. **Start the Application:**

   Launch the application with the following command:

    ```bash
    npm start
    ```

   The application will be accessible at [http://localhost:3000](http://localhost:3000).

## Usage

Upon launching the application, it automatically fetches match data from the API and generates betting slips, each
containing 4 matches. A virtual bet of 1000 TL is placed on each slip, and the application simulates the betting process
to calculate and display the potential profit or loss. The interface provides a detailed view of the betting slips and
match results.

## Deployment

To deploy the project using Firebase, execute the following command:

```bash
firebase deploy
```

Ensure that you have configured Firebase CLI with your project credentials.

## Contributing

We welcome contributions to improve Xsh Bet Simulator. If you have any ideas, suggestions, or bug reports, please open
an issue or submit a pull request.

## License

This project is licensed under the MIT License. For more details, please refer to the [LICENSE](LICENSE) file.
