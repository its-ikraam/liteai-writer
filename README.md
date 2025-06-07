# LiteAI Writer

LiteAI Writer is a lightweight, single-instance PHP application designed as a simple AI-powered writing assistant. Configure your preferred AI API, manage custom prompts, and quickly generate text based on built-in or your own templates.

This project aims to be an example of **effective public repositories** by providing a straightforward, useful tool with clear code and documentation, suitable for learning or simple personal use.

## Features

* **Lightweight:** Minimal dependencies, primarily plain PHP. Easy to deploy on any PHP-enabled server.
* **AI API Configuration:** Configure API Key, Endpoint URL, Model Name, and System Prompt via the web interface. (Supports OpenAI Chat Completion format by default, adaptable for similar APIs).
* **Prompt Management:** Comes with a few built-in prompts. Add, view, and delete your own custom prompts containing the `{user_input}` placeholder.
* **Simple Generation:** Select a prompt, enter your context/input, and generate text.
* **Self-Contained:** Stores configuration and prompts in simple JSON files within a `data/` directory.

## Screenshot (Optional - Add one later)

[Insert a screenshot of the UI here]

## Getting Started

### Prerequisites

* PHP 7.4 or higher (with `curl` and `json` extensions enabled, which are standard).
* A web server (like Apache or Nginx) configured to serve PHP files.
* An API Key from an AI provider (e.g., OpenAI).

### Installation

1. **Clone or Download:**
   
   ```bash
   git clone https://github.com/YOUR_USERNAME/liteai-writer.git
   cd liteai-writer
   ```
   
   Or download the ZIP and extract it.

2. **Permissions:** Ensure the web server has write permissions for the `data/` directory. LiteAI Writer needs this to save your configuration and custom prompts.
   
   ```bash
   # On Linux, navigate to the project root
   mkdir data
   chmod -R 775 data
   # Adjust ownership if necessary, e.g., chown -R www-data:www-data data
   # (775 might be too permissive, use 755 if possible, ensuring the web server user can write)
   ```
   
   *Security Note:* Check your server's security best practices for file permissions.

3. **Access:** Point your web browser to the `index.php` file on your server (e.g., `http://localhost/liteai-writer/` or `http://yourdomain.com/liteai-writer/`).

### Usage

1. **Configure API:** Expand the "API Configuration" section. Enter your AI provider's API Key, the correct API Endpoint URL (e.g., OpenAI's `https://api.openai.com/v1/chat/completions`), the model you want to use (e.g., `gpt-3.5-turbo`), and an optional system prompt. Click "Save Configuration".
   **⚠️ Security Warning:** Storing API keys directly in `config.json` is convenient for this simple tool but **insecure** for production or shared environments. Protect this file and consider more secure methods (like environment variables) for sensitive deployments.
2. **Manage Prompts (Optional):** Expand "Manage My Prompts" to add your own prompt templates. Ensure your template includes the placeholder `{user_input}` where your text from the main input area should be inserted. You can also delete prompts you no longer need.
3. **Generate Text:** Select a built-in or custom prompt from the dropdown menu. Enter your text/context into the main text area. Click "Generate". The result from the AI will appear below the form.

## Project Motivation & Acknowledgements

This project was created as part of a personal challenge and learning exercise. The goal is to reach 100 stars on GitHub.

Special mention to the **DartNode brand** - this project was partly inspired by an activity potentially offering server resources for achieving specific GitHub milestones, highlighting the synergy between open-source contributions and infrastructure support.

We believe in creating **effective public repositories** that are easy to understand, use, and potentially build upon.

## Contributing

Contributions, bug reports, and feature requests are welcome! Please feel free to open an issue or submit a pull request.

## License

This project is licensed under the MIT License - see the LICENSE file (if you add one) for details.

---
