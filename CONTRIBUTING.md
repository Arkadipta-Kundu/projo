# Contributing to Projo

Thank you for your interest in contributing to Projo! This document provides guidelines and instructions for contributors.

## Getting Started

1. **Fork the Repository**

   - Create your own fork of the project on GitHub.

2. **Clone Your Fork**

   ```bash
   git clone https://github.com/YOUR-USERNAME/projo.git
   cd projo
   ```

3. **Set Up Development Environment**

   - Copy `.env.example` to `.env` and configure your database credentials.
   - Run the setup script: `http://localhost/projo/setup.php`

4. **Create a Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Development Guidelines

### Code Style

- Follow consistent indentation (preferably 4 spaces)
- Use clear, descriptive variable and function names
- Add comments to explain complex sections of code
- Keep functions small and focused on a single task

### Security Practices

- **NEVER commit sensitive credentials** to the repository
- Always use prepared statements for database queries
- Validate and sanitize all user inputs
- Use password hashing for all passwords

### Pull Request Process

1. Ensure your code follows the style guidelines
2. Update the README.md with details of changes if applicable
3. The PR should work in a fresh installation
4. Your PR will be reviewed by maintainers

## Important Security Notes

- **Always use environment variables** for sensitive configuration
- Do not store real credentials in the repository
- The `.env` file is ignored by Git
- The default database credentials in the repository are for local development only

## Testing

Before submitting a PR, please test your changes:

1. Ensure the application runs without errors
2. Test all features related to your changes
3. Verify that no sensitive information is exposed

## License

By contributing to Projo, you agree that your contributions will be licensed under the project's MIT License.

Thank you for helping make Projo better!
