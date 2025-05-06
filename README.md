&nbsp;
<h1 align="center">EXPELLIARMUS MARKET</h1>
&nbsp;

<p align="center" style="font-size: 14px;">
    E-Commerce application for diploma project.
</p>

<p align="center" style="margin-top: 2rem;">
    <img alt="GitHub last commit (branch)" src="https://img.shields.io/github/last-commit/Igarevv/Expelliarmus-Market/master"/>
    <img alt="GitHub contributors" src="https://img.shields.io/github/contributors/Igarevv/Expelliarmus-Market"/>
    <img alt="GitHub top language" src="https://img.shields.io/github/languages/top/Igarevv/Expelliarmus-Market"/>
</p>

&nbsp;
<p align="center" style="font-size: 20px; margin-top: 2rem;">
    <strong>Built with the tools and technologies:</strong>
</p>

<p align="center" style="margin-top: 2rem;">
    <a href="https://www.php.net/">
        <img alt="PHP" src="https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white"/>
    </a>
    <a href="https://laravel.com/">
        <img alt="Laravel" src="https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white"/>
    </a>
    <a href="https://vuejs.org/">
        <img alt="Vue.js" src="https://img.shields.io/badge/vuejs-%2335495e.svg?style=for-the-badge&logo=vuedotjs&logoColor=%234FC08D"/>
    </a>
    <a href="https://tailwindcss.com/">
        <img alt="TailwindCSS" src="https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white"/>
    </a>
    <a href="https://getbootstrap.com/">
        <img alt="Bootstrap CSS" src="https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white"/>
    </a>
    <a href="https://www.docker.com">
        <img alt="Docker" src="https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white"/>
    </a>
    <a href="https://redis.io/">
        <img alt="Redis" src="https://img.shields.io/badge/redis-%23DD0031.svg?style=for-the-badge&logo=redis&logoColor=white"/>
    </a>
    <a href="https://www.postgresql.org/">
        <img alt="PostgreSQL" src="https://img.shields.io/badge/postgres-%23316192.svg?style=for-the-badge&logo=postgresql&logoColor=white"/>
    </a>
    <a href="https://azure.microsoft.com/">
        <img alt="Azure" src="https://img.shields.io/badge/azure-%230072C6.svg?style=for-the-badge&logo=microsoftazure&logoColor=white"/>
    </a>
    <a href="https://kubernetes.io/">
        <img alt="Kubernetes" src="https://img.shields.io/badge/kubernetes-%23326ce5.svg?style=for-the-badge&logo=kubernetes&logoColor=white"/>
    </a>
</p>

&nbsp;

## Table of Contents

- [Overview](#overview)
- [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
    - [Testing](#testing)
- [Modules](#modules)
- [Screenshots](#screenshots)

&nbsp;

## Overview

Expelliarmus-Market is an e-commerce web application that allows businesses to create, manage, and scale online stores
with ease.

### Key Features

- 🐳 **Docker Compose Configuration**: Simplifies service orchestration.
- 🔧 **Makefile Management**: Streamlines builds and processes.
- 🎨 **Tailwind CSS Integration**: Enhances UI consistency.
- 🔌 **API Integration**: Ensures seamless frontend-backend communication.
- 🔐 **Robust User Management**: Improves security and authentication.
- 📦 **Dynamic Product Management**: Efficient handling of inventory and discounts.

&nbsp;

## Getting Started

### Prerequisites

This project requires the latest installed Docker tool.
After cloning the application, you need to set **environment files**.

First, in the root directory of the project, you will find the [
`.env.docker.example`](https://github.com/Igarevv/Expelliarmus-Market/blob/master/.env.docker.example) file.

Then, in backend directory, you will find the [
`.env.example`](https://github.com/Igarevv/Expelliarmus-Market/blob/master/backend/.env.example) file.

And finally, in frontend directory of the project, you will find [
`.env.example`](https://github.com/Igarevv/Expelliarmus-Market/blob/master/frontend/.env-example) file.

By default, URL of api is - **api.expelliarmus.com:8080** and frontend **expelliarmus.com:3000** (dev mode) and
**expelliarmus.com:8000** (build mode).

You will need to set local domain name in
your [system hosts file](https://stackoverflow.com/questions/18200785/setting-up-local-domain-in-linux):

````
<your IPv4> expelliarmus.com

<your IPv4> api.expelliarmus.com
````

### Installation

Run following command:

````
make first-start-app
````

This command will run commands required for correct installation.

Next, you should determine in which mode to run frontend:

Developer mode:

````
make front-dev
````

Build mode:

````
make front-build
````

Additionally, you can create a **test super manager** to get access to the manager panel:

````
make super-manager
````

Manager login page available on **/management/manager/auth** URL.

For more commands see:

````
make help
````

### Testing

Backend application tests can be run using this command:

````
make backend-tests
````

&nbsp;

## Modules

Expelliarmus-Market is built with a modular architecture, allowing businesses to manage various aspects of their
e-commerce platform efficiently. Below is an overview of the core modules and the tasks they handle:

- **🛒 Order**  
  Handles cart functionality and order processing (excluding payments).
    - Users shopping cart functionality.
    - Create orders from the cart (checkout process without payment).
    - Track order status (e.g., pending, processing, shipped).
    - Enable managers to view, filter, and manage customer orders.

- **👤 User**  
  Provides robust user management for both customers and administrators.
    - Register and authenticate users with secure login.
    - Manage user roles and permissions (e.g., admin, customer).
    - Store and update user profiles and preferences.
    - Ability to conveniently interact with products by adding them to the cart and wishlist.

- **🧑‍💼 Manager**  
  Handles administrative functions for store managers.
    - Super managers can add and manage store managers.
    - Assign roles and manage access rights for different managers.
    - Create, edit, and delete discount coupons.
    - Track coupon usage and performance analytics.

- **📦 Product**  
  Handles product catalog management for the store.
    - Add, edit, and delete products with detailed attributes.
    - Manage product variants (e.g., sizes, colors).
    - Set discounts.


- **🏬 Warehouse**  
  Manages inventory and stock.
    - Track stock availability in real-time.
    - Ability to view all products information.
    - Manage restocking and inventory updates.


- **🏷️ Category**  
  Organizes products into categories for better navigation.
    - Create and manage product categories and subcategories.
    - Assign products to multiple categories.
    - Display category-based filters on the storefront.


- **🔖 Brand**  
  Manages brand information for products.
    - Add and edit brand details.
    - Associate products with specific brands.
    - Filter products by brand on the storefront.


- **📝 Content Management**  
  Enables dynamic content updates for the store.
    - Manage different content section such as Slider or New Arrivals section.


- **📊 Statistics**  
  Provides various statistics to improve user experience.

API documentation
available [here](https://documenter.getpostman.com/view/31662162/2sB2cSg3Ya#f83d1771-4c59-4db9-bebb-28b27e34b457).

&nbsp;

## Screenshots

### Web Application screenshots

### Shop

![Home Page](docs/app_overview/home.png)
<div style="text-align: center;">Pic.1 Home page</div>

![Products Page](docs/app_overview/products.png)
<div style="text-align: center;">Pic.2 Products page</div>

![Product Page](docs/app_overview/product_page.png)
<div style="text-align: center;">Pic.3 Product Page</div>

![Categories Overview](docs/app_overview/categories.png)
<div style="text-align: center;">Pic.4 Categories Overview</div>

![Category Products](docs/app_overview/by_categories.png)
<div style="text-align: center;">Pic.5 Products by categories</div>

![Brands Overview](docs/app_overview/brands.png)
<div style="text-align: center;">Pic.6 Brands Overview</div>

![Brands Products](docs/app_overview/by_brands.png)
<div style="text-align: center;">Pic.7 Products by brands</div>

![Cart](docs/app_overview/cart.png)
<div style="text-align: center;">Pic.8 Cart</div>

![Checkout Page](docs/app_overview/checkout.png)
<div style="text-align: center;">Pic.9 Checkout Page</div>

![Phone 1](docs/app_overview/account.png)
<div style="text-align: center;">Pic.10 Account</div>

![Coupons](docs/app_overview/coupons.png)
<div style="text-align: center;">Pic.11 Coupons</div>

![Order History](docs/app_overview/order_history.png)
<div style="text-align: center;">Pic.12 Order History</div>

### Management Panel

![Management Home Page](docs/app_overview/manager_home.png)
<div style="text-align: center;">Pic.13 Home Page</div>

![Users](docs/app_overview/manager_users.png)
<div style="text-align: center;">Pic.14 Users</div>

![Coupons](docs/app_overview/manager_coupons.png)
<div style="text-align: center;">Pic.15 Coupons Management</div>

![Orders](docs/app_overview/manager_orders.png)
<div style="text-align: center;">Pic.16 Orders</div>

![Products For Category](docs/app_overview/manager_all_products.png)
<div style="text-align: center;">Pic.17 Products in each category</div>

![Product Creation 1](docs/app_overview/manager_product_create1.png)
<div style="text-align: center;">Pic.18 Product creation</div>

![Product Creation 2](docs/app_overview/manager_product_create2.png)
<div style="text-align: center;">Pic.19 Product creation</div>

![Product Edit](docs/app_overview/manager_product_edit.png)
<div style="text-align: center;">Pic.20 Product edit</div>

![Categories](docs/app_overview/manager_categories.png)
<div style="text-align: center;">Pic.21 Categories Management</div>

![Brands](docs/app_overview/manager_brands.png)
<div style="text-align: center;">Pic.22 Brands Management</div>

![Trash](docs/app_overview/manager_trash.png)
<div style="text-align: center;">Pic.23 Products Trash</div>

![Warehouse 1](docs/app_overview/management_warehouse_products.png)
<div style="text-align: center;">Pic.24 Warehouse</div>

![Warehouse 2](docs/app_overview/management_warehouse_product.png)
<div style="text-align: center;">Pic.25 Warehouse</div>

![Discounted Products](docs/app_overview/management_warehouse_discounts.png)
<div style="text-align: center;">Pic.26 Discounted Products</div>

![Discount creation](docs/app_overview/management_add_discount.png)
<div style="text-align: center;">Pic.27 Discount creation</div>

![Slider](docs/app_overview/manager_slider.png)
<div style="text-align: center;">Pic.28 Slider Content Management</div>

![New Arrivals](docs/app_overview/manager_new_arrivals.png)
<div style="text-align: center;">Pic.28 New Arrivals Content Management</div>

### Database Diagram

![Expelliarmus Shop Database Diagram](./docs/diagrams/expelluarmus_database_diagram.png)

or [download .drawio file](https://raw.githubusercontent.com/BackLoveEnd/Expelliarmus-Market/refs/heads/master/docs/diagrams/expelliarmus_diagram_database.drawio) (
on the open page: Right Click -> Save as) and open diagram with [draw.io](https://app.diagrams.net/).

### Modules Diagrams

Diagrams for each modules
available [here](https://github.com/BackLoveEnd/Expelliarmus-Market/tree/master/docs/uml/modules).