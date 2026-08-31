# Miamy

*[Version française disponible ici](README.fr.md)*

Miamy is an online food ordering platform, a bit like Uber Eats, that I built on my own during an internship at YOUONLINE. It has three separate spaces, one for customers who browse restaurants and place orders, one for restaurant owners who manage their menu and opening hours, and one for admins who oversee the whole platform.

## What it does

Customers can browse the list of restaurants, look at a restaurant's menu and details, and place an order. Restaurant owners get their own dashboard where they manage their dishes (add, edit, delete, toggle availability), reorder them by drag and drop within a category, and set their opening hours day by day. Admins can manage restaurants and users across the platform, and every sensitive action (role changes, deletions) gets logged so there's a trace of who did what.

The dish management screen is the part I'm most proud of: reordering dishes by drag and drop and flipping their availability both happen through AJAX calls, so the page never reloads, and the new order is saved straight to the database.

## How it's built

There's no framework, everything is hand rolled PHP, organized around a front controller. `index.php` receives every request, looks up the requested page in a routing table that maps a page name to a controller and a method, and dispatches to it. Business logic sits in a set of classes (`classes/class.users.php`, `class.restaurants.php`, `class.plats.php`, and so on), the database connection is a PDO singleton that also aligns MySQL's time zone with Europe/Paris so timestamps come out right, and every sensitive action checks both the user's role and that they actually own the resource they're touching, a restaurant owner can only edit their own dishes, for instance. Passwords are hashed with bcrypt, and output is escaped with `htmlspecialchars` to avoid XSS.

**Stack:** PHP (OOP, no framework), MySQL / PDO, JavaScript (Fetch / AJAX), SortableJS, Bootstrap, HTML / CSS.

## Running it locally

You'll need PHP, MySQL and a local server (I use XAMPP). Clone the repo, import `Miamy.sql` into MySQL to create the schema, then copy `.env.example` to `.env` and fill in your local database host, user, password and name, plus a value for `BASE_SALT`. Drop the project into your server's web root and open `index.php` in the browser, the app detects whether it's running locally or in production and adjusts its base URL automatically.

## About me

I'm Maxime Paulin, a web developer who just finished a DWWM qualification and is now continuing with a CDA (Concepteur Développeur d'Applications) training at CESI. I'm looking for a work study placement for next year. The site is no longer hosted online, since that was tied to the length of the internship, but the full source code is here.

[LinkedIn](https://www.linkedin.com/in/maxime-paulin-968ab1266/) · [GitHub](https://github.com/meagle-pixel)
