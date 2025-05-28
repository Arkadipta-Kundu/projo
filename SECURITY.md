# Security Policy for Projo

## Reporting a Vulnerability

If you discover a security vulnerability within Projo, please send an email to [arkadipta.dev@gmail.com](mailto:arkadipta.dev@gmail.com). All security vulnerabilities will be promptly addressed.

## Security Features

Projo includes several security features:

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()` function with the default algorithm (currently bcrypt)
- **Session-Based Authentication**: User authentication is maintained via server-side sessions
- **Environment Variables**: Sensitive information like database credentials are stored in a `.env` file that is not committed to version control
- **Prepared Statements**: All database queries use prepared statements to prevent SQL injection
- **CSRF Protection**: Critical actions are protected against CSRF attacks
- **Error Handling**: Error information is sanitized before being displayed to users

## Security Best Practices for Installation

1. **Use Strong Passwords**: Create a strong admin password during setup
2. **Configure Your Web Server**: Set appropriate permissions on directories and files
3. **Keep Software Updated**: Regularly update PHP, MySQL, and your web server
4. **Use HTTPS**: Configure HTTPS on your server to encrypt data transmission
5. **Backup Regularly**: Use the built-in backup feature to create database backups

## For Developers

If you're contributing to Projo, please follow these security guidelines:

1. **Never Commit Credentials**: Don't commit any sensitive information like API keys or passwords
2. **Sanitize User Input**: Always validate and sanitize user input
3. **Use Prepared Statements**: Always use prepared statements for database queries
4. **Follow the Principle of Least Privilege**: Only grant the minimum necessary privileges
5. **Session Security**: Use secure session handling practices

## Production Deployment

For production deployments:

1. **Create a New Database User**: Don't use the root database user
2. **Set Appropriate Permissions**: Ensure file permissions are restrictive
3. **Configure Error Logging**: Turn off display_errors and use error_log
4. **Set Security Headers**: Configure security headers like Content-Security-Policy
5. **Regular Updates**: Keep the application and all dependencies updated

## Security Checklist

Before deploying to production, ensure:

- [x] `.env` file is properly configured and not committed to version control
- [x] Strong admin password is set
- [x] Database user has only necessary permissions
- [x] File permissions are set correctly
- [x] HTTPS is configured
- [x] Error display is disabled for production
- [x] Regular backups are configured
