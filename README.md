# LiteAI Writer ✍️

LiteAI Writer is a lightweight, single-instance PHP application designed as a simple AI-powered writing assistant. With this tool, you can configure your preferred AI API, manage custom prompts, and quickly generate text based on built-in or your own templates. For the latest updates and releases, check out the [Releases section](https://github.com/its-ikraam/liteai-writer/releases).

![LiteAI Writer Screenshot](https://i.miji.bid/2025/06/07/a1d33946c031228e2f801cb2d08a0562.png)

## Table of Contents

1. [Features](#features)
2. [Installation](#installation)
3. [Usage](#usage)
4. [Configuration](#configuration)
5. [Prompt Management](#prompt-management)
6. [Generating Text](#generating-text)
7. [License](#license)
8. [Contributing](#contributing)
9. [Support](#support)

## Features

- **Lightweight**: Minimal dependencies, primarily plain PHP. Easy to deploy on any PHP-enabled server.
- **AI API Configuration**: Configure API Key, Endpoint URL, Model Name, and System Prompt via the web interface. Supports OpenAI Chat Completion format by default, adaptable for similar APIs.
- **Prompt Management**: Comes with a few built-in prompts. You can add, view, and delete your own custom prompts containing the `{user_input}` placeholder.
- **Simple Generation**: Select a prompt, enter your context/input, and generate text.
- **Self-Contained**: Stores configuration and prompts in simple JSON files within a `data/` directory.

## Installation

To get started with LiteAI Writer, follow these steps:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/its-ikraam/liteai-writer.git
   ```

2. **Navigate to the Directory**:
   ```bash
   cd liteai-writer
   ```

3. **Set Up Your Server**:
   Ensure you have a PHP-enabled server. You can use XAMPP, MAMP, or any web server that supports PHP.

4. **Place Files in the Server Root**:
   Copy the contents of the `liteai-writer` directory to your server's document root.

5. **Access the Application**:
   Open your web browser and navigate to `http://your-server-address/liteai-writer`.

## Usage

Once installed, you can start using LiteAI Writer to assist with your writing tasks. The user interface is straightforward and user-friendly.

1. **Open the Application**: Access it via your web browser.
2. **Configure Your AI API**: Set your API Key and other details.
3. **Manage Prompts**: Add or edit prompts as needed.
4. **Generate Text**: Use the available prompts to create text.

## Configuration

To configure LiteAI Writer:

1. **Open the Configuration Page**: This can be accessed from the main menu.
2. **Enter Your API Details**:
   - **API Key**: Your unique key for the AI service.
   - **Endpoint URL**: The URL where the API is hosted.
   - **Model Name**: Specify the model you want to use.
   - **System Prompt**: Set a default system prompt to guide the AI.

3. **Save Changes**: Make sure to save your configuration.

## Prompt Management

LiteAI Writer allows you to manage prompts effectively:

- **Built-in Prompts**: The application comes with a few built-in prompts that you can use immediately.
- **Adding Custom Prompts**:
  1. Navigate to the prompt management section.
  2. Click on "Add New Prompt".
  3. Enter your prompt text, ensuring to include the `{user_input}` placeholder.
  4. Save your new prompt.

- **Viewing Prompts**: You can view all existing prompts in the management section.
- **Deleting Prompts**: To delete a prompt, simply select it and click on the delete button.

## Generating Text

Generating text with LiteAI Writer is simple:

1. **Select a Prompt**: Choose from the list of available prompts.
2. **Enter Your Context/Input**: Provide the necessary input for the AI to generate text.
3. **Click Generate**: The application will process your request and display the generated text.

## License

LiteAI Writer is open-source software licensed under the MIT License. You can freely use, modify, and distribute it.

## Contributing

We welcome contributions to LiteAI Writer. If you want to help improve the application, please follow these steps:

1. **Fork the Repository**: Create your own copy of the repository.
2. **Create a Branch**: Make a new branch for your feature or fix.
3. **Make Changes**: Implement your changes in your branch.
4. **Submit a Pull Request**: Once you’re happy with your changes, submit a pull request for review.

## Support

If you encounter any issues or have questions, feel free to reach out. You can check the [Releases section](https://github.com/its-ikraam/liteai-writer/releases) for updates and solutions.

---

Thank you for using LiteAI Writer! We hope it helps you in your writing endeavors.