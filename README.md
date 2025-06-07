# LiteAI Writer

LiteAI Writer is a lightweight, single-instance PHP application designed as a simple AI-powered writing assistant. Configure your preferred AI API, manage custom prompts, and quickly generate text based on built-in or your own templates.

## Features

* **Lightweight:** Minimal dependencies, primarily plain PHP. Easy to deploy on any PHP-enabled server.
* **AI API Configuration:** Configure API Key, Endpoint URL, Model Name, and System Prompt via the web interface. (Supports OpenAI Chat Completion format by default, adaptable for similar APIs).
* **Prompt Management:** Comes with a few built-in prompts. Add, view, and delete your own custom prompts containing the `{user_input}` placeholder.
* **Simple Generation:** Select a prompt, enter your context/input, and generate text.
* **Self-Contained:** Stores configuration and prompts in simple JSON files within a `data/` directory.

## Screenshot 

![a1d33946c031228e2f801cb2d08a0562.png](https://i.miji.bid/2025/06/07/a1d33946c031228e2f801cb2d08a0562.png)

### Usage

1. **Configure API:** Expand the "API Configuration" section. Enter your AI provider's API Key, the correct API Endpoint URL (e.g., OpenAI's `https://api.openai.com/v1/chat/completions`), the model you want to use (e.g., `gpt-3.5-turbo`), and an optional system prompt. Click "Save Configuration".
   **⚠️ Security Warning:** Storing API keys directly in `config.json` is convenient for this simple tool but **insecure** for production or shared environments. Protect this file and consider more secure methods (like environment variables) for sensitive deployments.
2. **Manage Prompts (Optional):** Expand "Manage My Prompts" to add your own prompt templates. Ensure your template includes the placeholder `{user_input}` where your text from the main input area should be inserted. You can also delete prompts you no longer need.
3. **Generate Text:** Select a built-in or custom prompt from the dropdown menu. Enter your text/context into the main text area. Click "Generate". The result from the AI will appear below the form.

## Contributing

Contributions, bug reports, and feature requests are welcome! Please feel free to open an issue or submit a pull request.

---
