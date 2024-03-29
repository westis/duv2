# DUV Ultramarathon Statistics Frontend (WIP)

Welcome to the repository for the new frontend of the DUV Ultramarathon Statistics website. This project is currently a work-in-progress and aims to provide an enhanced user experience with a modernized interface and improved SEO capabilities.

## Technology Stack

This project is built with a robust and modern stack, focusing on performance, developer experience, and future scalability:

- **[Vue 3](https://vuejs.org/)**: The progressive JavaScript framework for building modern web interfaces, utilizing the Composition API for better organization and logic reuse.
- **[Nuxt 3](https://v3.nuxtjs.org/)**: The hybrid Vue framework that provides server-side rendering, static site generation, and powerful tooling for a top-notch developer experience.
- **[Vuetify 3](https://vuetifyjs.com/)**: A Vue UI library with a complete set of handcrafted Material components for building rich and engaging user interfaces.
- **[Pinia](https://pinia.vuejs.org/)**: The official state management solution for Vue, offering an intuitive and flexible way to manage global state in apps.
- **[VueUse](https://vueuse.org/)**: A collection of utility functions based on the Composition API to enhance the composition and reactivity in Vue applications.

## Project Status: Work-in-Progress

Please note that the codebase is under active development. The frontend is not yet in a production-ready state and will continue to evolve with new features, optimizations, and refinements.

### Current Features

- Events list for future and past events, with filters based on existing JSON API.
- Event results page.

### Planned Features

- Other pages based on existing JSON API (event details, runner profiles)
- Updated API to include other features of the current website, such as Search (for runners and events), top lists/rankings, summary statistics and championships.
- Improved API for better user experience, including date range, better search functionality and more targeted data fetching with pagination.
- New features, such as ??

## Using VueUse

We leverage VueUse to streamline our compositional logic and provide reactive utilities that simplify complex scenarios. For example, we use utilities like `useAxios` for making reactive API calls that automatically update our components when data changes. This allows us to focus on the business logic without reinventing the wheel for common reactive patterns.

## Getting Started

To contribute to this project or run it locally, please follow these steps:

```bash
# Clone the repository
git clone https://github.com/<your-username>/duv2.git

# Navigate to the project directory
cd duv2

# Install dependencies
npm install

# Serve with hot reload at localhost:3000
npm run dev
```
