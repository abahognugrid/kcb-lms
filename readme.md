# LMS

[![CI](https://github.com/GNU-GRID-AFRICA/lms/actions/workflows/build.yml/badge.svg)](https://github.com/GNU-GRID-AFRICA/lms/actions/workflows/build.yml)

## Getting started
1. Install & start [Docker](https://docs.docker.com/install/#supported-platforms)
1. Run these instructions:
```bash
   git clone git@github.com:GNU-GRID-AFRICA/lms.git
   cd lms
   cp .env.example .env
   docker-compose up -d lms

   php artisan migrate:fresh --seed
```
2. Visit `http://localhost:8082` in your browser
2. **Log In** with these email / password credentials: `admin@lms.com / password`

