# HOG Timer

## Overview
HOG Timer is a simple web application designed to provide a countdown timer for events. It allows user/the church to set a timer duration in minutes and displays a countdown on the screen. The background color changes based on the percentage of time remaining, enhancing the visual experience as the timer progresses.

## Features
- User-friendly interface to set the timer duration in minutes.
- Countdown display in hours, minutes, and seconds.
- Visual feedback through background color changes based on the time remaining.
- Notification when the timer has expired, along with a display of how many minutes have passed since expiration.

## Files
The project consists of two main HTML files:
1. **entrypoint.html**: The landing page where users can input the desired timer duration.
2. **timer-bl4ckdust.html**: The timer page that displays the countdown and handles the timer logic.

### File Descriptions
- **entrypoint.html**
  - Contains an input field for users to enter the timer duration in minutes.
  - A button to start the timer, which redirects to the timer page with the duration as a URL parameter.

- **timer-bl4ckdust.html**
  - Displays the countdown timer.
  - Changes background color based on the remaining time.
  - Alerts the user when the timer has expired and starts counting the minutes past the expiration.

## Usage
1. Open `entrypoint.html` in your web browser.
2. Enter the desired timer duration in minutes in the input field.
3. Click the "Start Timer" button to begin the countdown.
4. The timer will display the remaining time and change colors as it progresses.
5. Once the timer reaches zero, it will notify you that the time has expired and will start counting how many minutes have passed.

## Requirements
- A modern web browser (Chrome, Firefox, Safari, etc.) to run the application.
- Internet connection to load Bootstrap CSS and JS from CDN.

## Installation
To run the application locally:
1. Download the project files.
2. Open `entrypoint.html` in your web browser to start using the timer.

## License
This project is open-source and available for anyone to use and modify. 

Feel free to contribute to the project or suggest improvements!

## Acknowledgments
- Bootstrap for styling the application.
- JavaScript for implementing the timer functionality.
