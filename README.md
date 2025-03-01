# Puf - PHP ultralight framework

Puf is a **minimalist PHP framework** designed for simplicity, speed, and ease of use. It eliminates the bloat of traditional frameworks by using **flat JSON files instead of databases**, **magic link authentication instead of passwords**, and **straightforward routing and templating**.


## 🚀 Features

### Simple Routing
Puf includes an intuitive **router** that allows you to define clean, RESTful routes with minimal setup. Example:

### Handlebars-Based Templating
This keeps logic separate from presentation while allowing for dynamic content rendering.

### Flat-File JSON Storage (No Database!)
Instead of using a database, Puf stores all data as JSON files inside project/data/.

### Magic Link Authentication (No Passwords!)
Instead of passwords, Puf exclusively uses magic links for authentication.
Users enter their email, and Puf generates a one-time-use login link that logs them in instantly.


# 💡 Why Use Puf?

✅ Zero config – No databases, no migrations, just PHP.
✅ Flat-file JSON storage – Fast, lightweight, and easy to work with.
✅ Secure authentication – No passwords, only magic links.
✅ Perfect for small projects & personal apps.


# ⚡️ Future Plans

    📧 Magic Link Email Sending (Currently returns token as text)
    📜 Middleware Support (For request handling)
    📁 More Examples (User authentication, settings)


# 👾 Contributing

Puf is open-source and welcomes contributions! Fork, modify, and submit a pull request.

🏗 Built by fufroom