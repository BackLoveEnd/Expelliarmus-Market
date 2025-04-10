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
    <a href="https://www.docker.com">
        <img alt="Docker" src="https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white"/>
    </a>
    <a href="https://redis.io/">
        <img alt="Redis" src="https://img.shields.io/badge/redis-%23DD0031.svg?style=for-the-badge&logo=redis&logoColor=white"/>
    </a>
    <a href="https://www.postgresql.org/">
        <img alt="PostgreSQL" src="https://img.shields.io/badge/postgres-%23316192.svg?style=for-the-badge&logo=postgresql&logoColor=white"/>
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
172.0.0.1 expelliarmus.com

172.0.0.1 api.expelliarmus.com
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


- **👤 User**  
  Provides robust user management for both customers and administrators.
    - Register and authenticate users with secure login.
    - Manage user roles and permissions (e.g., admin, customer).
    - Store and update user profiles and preferences.
    - Ability to conveniently interact with products by adding them to the cart and wishlist.


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

### Database Diagram

### Modules Diagrams
