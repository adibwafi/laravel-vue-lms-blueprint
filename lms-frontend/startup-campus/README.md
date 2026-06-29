# AcmeLMS Frontend

Learning Management System (LMS) frontend application for Acme EdTech, built with Vue.js 2, Vuetify, and modern web technologies.

## 📋 Table of Contents
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Project Setup](#project-setup)
- [Development](#development)
- [Environment Variables](#environment-variables)
- [Project Structure](#project-structure)
- [Deployment](#deployment)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

## 🛠 Tech Stack

### Core Framework & UI
- **Vue.js 2.6.14** - Progressive JavaScript framework
- **Vue Router 3.5.1** - Official router for Vue.js
- **Vuetify 2.6.0** - Material Design component framework
- **Vue CLI 5.0.0** - Standard tooling for Vue.js development

### Key Dependencies
- **Axios 0.27.2** - HTTP client for API calls
- **CKEditor5** - Rich text editor for content management
- **Moment.js** - Date/time manipulation
- **DOMPurify** - XSS sanitization
- **Vue Meta** - Manage page metadata
- **Universal Cookie** - Cookie management
- **Vue PDF Embed** - PDF viewing capability
- **QRCode.vue** - QR code generation
- **Vue HTML2PDF** - PDF export functionality

### Development Tools
- **ESLint** - Code linting with Prettier integration
- **Cypress 13.13.3** - E2E testing framework
- **Sass** - CSS preprocessor
- **Babel** - JavaScript transpiler

## 📦 Prerequisites

Before you begin, ensure you have the following installed:
- **Node.js**: LTS version (recommended: v16.x or v18.x)
- **npm**: v7.x or higher (comes with Node.js)
- **Git**: For version control
- **Docker**: (Optional) For containerized deployment

## 🚀 Project Setup

### 1. Clone the Repository
```bash
git clone https://github.com/adibwafi/laravel-vue-lms-blueprint.git
cd lms-frontend/startup-campus
```

### 2. Install Dependencies
```bash
npm install
```

### 3. Update Browserslist Database (if needed)
```bash
npx update-browserslist-db@latest
```

## 💻 Development

### Start Development Server
```bash
npm run serve
```
The application will be available at `http://localhost:8080` (default port).

### Build for Production
```bash
npm run build
```
Production-ready files will be generated in the `dist/` directory.

### Lint and Fix Files
```bash
npm run lint
```
Automatically fixes linting issues based on ESLint + Prettier configuration.

### Run E2E Tests
```bash
npx cypress open    # Opens Cypress Test Runner
npx cypress run     # Runs tests in headless mode
```

## 🔐 Environment Variables

The application requires environment variables for configuration. These are managed differently for local development and CI/CD pipelines.

### Local Development

Create a `.env` file in the `lms-frontend/` directory:

```bash
VUE_APP_STORAGE_BASE_URL="https://your-s3-bucket.s3.your-region.amazonaws.com/"
VUE_APP_API_BASE_URL="http://localhost:8000"  # or your API endpoint
```

### Production (.env.production)

The `.env.production` file contains production values:
```bash
VUE_APP_STORAGE_BASE_URL="https://your-s3-bucket.s3.your-region.amazonaws.com/"
VUE_APP_API_BASE_URL="https://http://localhost:8000"
```

### Environment Variables Explained

| Variable | Description |
|----------|-------------|
| `VUE_APP_API_BASE_URL` | Backend API base URL |
| `VUE_APP_STORAGE_BASE_URL` | AWS S3 bucket URL for static assets |
| `CYPRESS_BASE_URL` | Base URL for Cypress E2E tests |

**⚠️ Important Notes:**
- `.env` files are gitignored for security
- Never commit sensitive credentials to the repository
- GitHub Actions uses repository secrets/variables for CI/CD
- `VUE_APP_API_BASE_URL` is required and validated in `main.js`

## 📁 Project Structure

```
lms-frontend/
├── public/                  # Static assets
│   └── index.html          # HTML template
├── src/
│   ├── assets/             # Images, styles, certificates
│   │   ├── cert/          # Certificate templates
│   │   ├── css/           # Global styles (SCSS)
│   │   └── img/           # Images
│   ├── components/         # Reusable Vue components
│   │   ├── Alert.vue
│   │   ├── HeaderApp.vue
│   │   ├── HeaderCMS.vue
│   │   ├── Loading.vue
│   │   ├── NavbarDesktop.vue
│   │   ├── PdfViewer.vue
│   │   ├── SidebarCMS.vue
│   │   ├── UploadAdapter.js  # CKEditor upload adapter
│   │   └── ...
│   ├── composables/        # Vue composition API utilities
│   ├── helpers/            # Helper functions
│   │   └── deviceDetect.js
│   ├── plugins/            # Vue plugins
│   │   └── vuetify.js     # Vuetify configuration
│   ├── router/             # Vue Router configuration
│   │   └── index.js       # Route definitions
│   ├── utils/              # Utility functions
│   │   └── userAgent.js
│   ├── views/              # Page components
│   │   ├── admin/         # CMS/Admin panel pages
│   │   │   ├── admin/     # Admin management
│   │   │   ├── course/    # Course management
│   │   │   ├── exam/      # Exam & question bank
│   │   │   ├── expert/    # Expert management
│   │   │   ├── feedback/  # User feedback
│   │   │   ├── lesson/    # Lesson management
│   │   │   ├── promo/     # Promotion management
│   │   │   ├── tracking/  # User tracking
│   │   │   ├── user/      # User management
│   │   │   └── voucher/   # Voucher management
│   │   └── website/       # Public-facing pages
│   │       ├── auth/      # Authentication (login, register, etc.)
│   │       ├── certificate/  # Certificate viewing/download
│   │       ├── course/    # Course catalog & details
│   │       ├── exam/      # Student exam interface
│   │       ├── home/      # Homepage & promotions
│   │       ├── lesson/    # Lesson viewer
│   │       ├── profile/   # User profile
│   │       └── voucher/   # Voucher redemption
│   ├── App.vue            # Root component
│   └── main.js            # Application entry point
├── cypress/               # E2E tests
│   ├── e2e/
│   │   └── login.cy.js   # Login flow tests
│   ├── fixtures/
│   └── support/
├── .env.production        # Production environment variables
├── .gitignore            # Git ignore rules
├── babel.config.js       # Babel configuration
├── build-and-deploy.sh   # Legacy deployment script
├── cypress.config.js     # Cypress configuration
├── Dockerfile            # Docker build configuration
├── jsconfig.json         # JavaScript configuration
├── package.json          # Dependencies & scripts
├── vue.config.js         # Vue CLI configuration
└── README.md            # This file
```

### Key Directories Explained

- **views/admin/** - CMS for managing courses, lessons, exams, users, etc. (requires authentication)
- **views/website/** - Student-facing pages for learning, taking exams, viewing certificates
- **components/** - Shared UI components used across the application
- **router/** - Routing configuration with authentication guards
- **assets/css/** - Global SCSS styles

## 🚀 Deployment

The application uses **GitHub Actions** for automated CI/CD deployment to AWS infrastructure.

### Deployment Architecture

- **Staging Environment**: Automatically deployed on Pull Request creation/update
- **Production Environment**: Automatically deployed when PR is merged to `main`
- **Infrastructure**: AWS App Runner with Docker containers stored in ECR

### Branch Strategy

- `main` - Production branch
- `staging` - Staging branch (if used)
- Feature branches - Create from `main` for new features

### Automated CI/CD Workflow

#### 1. Staging Deployment (on Pull Request)

**Trigger**: When a PR is opened or updated against `main`

Workflow: `.github/workflows/startup-campus-on-pull-request.yml`

Steps:
1. Checkout code
2. Configure AWS credentials
3. Login to Amazon ECR
4. Replace `.env` with staging variables from GitHub Secrets
5. Build Docker image with tags:
   - `{commit-hash}`
   - `latest-staging`
6. Push images to ECR
7. Update AWS App Runner (staging)

**Result**: Changes are live on staging environment for testing.

#### 2. Production Deployment (on PR Merge)

**Trigger**: When PR is merged to `main`

Workflow: `.github/workflows/startup-campus-on-merge.yml`

Steps:
1. Checkout code
2. Configure AWS credentials
3. Login to Amazon ECR
4. Replace `.env` with production variables from GitHub Secrets
5. Build Docker image with tags:
   - `{commit-hash}`
   - `latest`
6. Push images to ECR
7. Update AWS App Runner (production)

**Result**: Changes are live in production.

### Required GitHub Secrets

Configure these in your GitHub repository settings:

| Secret/Variable | Type | Description |
|----------------|------|-------------|
| `AWS_ACCESS_KEY_ID` | Secret | AWS access key for ECR/App Runner |
| `AWS_SECRET_ACCESS_KEY` | Secret | AWS secret key |
| `REPO_NAME` | Secret | ECR repository name |
| `VUE_APP_STORAGE_BASE_URL` | Variable | S3 bucket URL |
| `VUE_APP_API_BASE_URL_STAGING` | Variable | Staging API URL |
| `VUE_APP_API_BASE_URL_PRODUCTION` | Variable | Production API URL |

### Docker Deployment

The application is containerized using a multi-stage Dockerfile:

```dockerfile
# Stage 1: Build
FROM node:lts-alpine as build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Serve with Nginx
FROM nginx:stable-alpine as production-stage
COPY --from=build-stage /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

### Legacy Deployment (Manual - Deprecated)

**Note**: This method is deprecated in favor of GitHub Actions.

The `build-and-deploy.sh` script was previously used for manual rsync deployment:

```bash
# Legacy method (not recommended)
chmod 400 your-key.pem
chmod +x build-and-deploy.sh
./build-and-deploy.sh
```

## 🧪 Testing

### E2E Testing with Cypress

The project includes Cypress for end-to-end testing.

**Run Tests Interactively:**
```bash
npx cypress open
```

**Run Tests in CI Mode:**
```bash
npx cypress run
```

**Test Files Location:** `cypress/e2e/`

**Cypress Configuration:** `cypress.config.js`

Environment variables for Cypress are loaded from `.env` via `dotenv`.

### Current Test Coverage

- Login flow (`cypress/e2e/login.cy.js`)

**Note**: Expand test coverage as the application grows.

## 🔧 Troubleshooting

### Common Issues

#### 1. `VUE_APP_API_BASE_URL is not defined`

**Problem**: Application crashes on startup.

**Solution**: Ensure `.env` file exists with required variables:
```bash
VUE_APP_API_BASE_URL="http://localhost:8000"
```

#### 2. Browserslist Database Outdated

**Problem**: Warning during build/lint.

**Solution**:
```bash
npx update-browserslist-db@latest
```

#### 3. Build Fails / Dependencies Issue

**Problem**: Build errors after pulling latest code.

**Solution**:
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

#### 4. CORS Issues in Development

**Problem**: API calls fail due to CORS.

**Solution**: 
- Configure backend to allow `http://localhost:8080`
- Or use a proxy in `vue.config.js`:
```javascript
devServer: {
  proxy: {
    '/api': {
      target: 'http://localhost:8000',
      changeOrigin: true
    }
  }
}
```

#### 5. Docker Build Fails

**Problem**: Docker build issues.

**Solution**:
- Ensure `.env` file is created (not ignored during build context)
- Check Docker has sufficient resources
- Clear Docker cache: `docker system prune -a`

#### 6. AWS Deployment Issues

**Problem**: GitHub Actions workflow fails.

**Solution**:
- Verify AWS credentials in GitHub Secrets
- Check ECR repository exists
- Verify App Runner service configuration
- Check GitHub Actions logs for specific errors

### Getting Help

- **Internal Team**: Contact your supervisor or team lead
- **Documentation**: Refer to [Vue.js docs](https://v2.vuejs.org/), [Vuetify docs](https://v2.vuetifyjs.com/)
- **Issues**: Check GitHub Issues for known problems

## 🤝 Contributing

### Workflow for New Features

1. **Create Feature Branch**
   ```bash
   git checkout main
   git pull origin main
   git checkout -b feature/your-feature-name
   ```

2. **Develop & Test Locally**
   - Make changes
   - Test thoroughly with `npm run serve`
   - Run linter: `npm run lint`
   - Test build: `npm run build`

3. **Commit Changes**
   ```bash
   git add .
   git commit -m "feat: add your feature description"
   ```

4. **Push & Create PR**
   ```bash
   git push origin feature/your-feature-name
   ```
   Create Pull Request on GitHub targeting `main` branch.

5. **Review on Staging**
   - GitHub Actions automatically deploys to staging
   - Test your changes on staging environment
   - Request code review from team members

6. **Merge to Production**
   - Once approved, merge PR to `main`
   - GitHub Actions automatically deploys to production
   - Monitor production for any issues

### Commit Message Convention

Follow [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `style:` - Code style changes (formatting)
- `refactor:` - Code refactoring
- `test:` - Adding or updating tests
- `chore:` - Maintenance tasks

### Code Style

- Follow ESLint + Prettier configuration
- Run `npm run lint` before committing
- Use Vue.js best practices
- Keep components small and reusable
- Document complex logic with comments

## 📝 Additional Resources

- [Vue.js 2 Documentation](https://v2.vuejs.org/)
- [Vuetify 2 Documentation](https://v2.vuetifyjs.com/)
- [Vue Router Documentation](https://router.vuejs.org/)
- [Vue CLI Documentation](https://cli.vuejs.org/)
- [Cypress Documentation](https://docs.cypress.io/)

## 📄 License

See [LICENSE](../LICENSE) file for details.

---

**Last Updated**: November 2025  
**Maintained by**: Muhamad Adibwafi Menako