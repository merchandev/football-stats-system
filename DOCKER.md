# 🐳 Docker Deployment Guide

This guide explains how to deploy the Football Statistics System using Docker and specifically for Hostinger VPS.

## 📋 Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git
- 2GB RAM minimum (4GB recommended)
- 10GB disk space

## 🚀 Quick Start (Local Development)

### 1. Clone the Repository

```bash
git clone https://github.com/merchandev/football-stats-system.git
cd football-stats-system
```

### 2. Configure Environment Variables

```bash
cp .env.example .env
```

Edit `.env` and set your database credentials:

```env
MYSQL_ROOT_PASSWORD=your_secure_root_password
MYSQL_DATABASE=football_stats
MYSQL_USER=football_user
MYSQL_PASSWORD=your_secure_password
VITE_API_URL=http://localhost:3000/api
```

### 3. Build and Start Services

```bash
# Build all Docker images
docker-compose build

# Start all services in detached mode
docker-compose up -d
```

### 4. Verify Deployment

Check that all services are running:

```bash
docker-compose ps
```

You should see 3 services running:
- `football_stats_db` - MySQL database
- `football_stats_backend` - PHP API
- `football_stats_frontend` - React UI

### 5. Access the Application

- **Frontend**: http://localhost
- **Backend API**: http://localhost:3000/api
- **MySQL**: localhost:3306

## 🌐 Hostinger VPS Deployment

### Step 1: Connect to Your VPS

```bash
ssh root@your-vps-ip
```

### Step 2: Install Docker (if not installed)

```bash
# Update package index
apt update

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Install Docker Compose
apt install docker-compose-plugin -y

# Verify installation
docker --version
docker compose version
```

### Step 3: Clone Your Repository

```bash
cd /opt
git clone https://github.com/merchandev/football-stats-system.git
cd football-stats-system
```

### Step 4: Configure for Production

Create `.env` file:

```bash
nano .env
```

Set production values:

```env
MYSQL_ROOT_PASSWORD=STRONG_PASSWORD_HERE
MYSQL_DATABASE=football_stats
MYSQL_USER=football_user
MYSQL_PASSWORD=STRONG_PASSWORD_HERE
VITE_API_URL=http://your-domain.com:3000/api
NODE_ENV=production
```

**Important**: Replace `your-domain.com` with your actual domain or VPS IP address.

### Step 5: Update Frontend API URL

Before building, you need to configure the frontend to point to your production API URL.

Edit `frontend/src/services/api.js` (if it exists) or set the environment variable in `.env`:

```env
VITE_API_URL=http://your-domain.com:3000/api
```

### Step 6: Build and Start

```bash
# Build images
docker compose build

# Start services
docker compose up -d

# Check logs
docker compose logs -f
```

### Step 7: Configure Firewall

Allow HTTP traffic:

```bash
ufw allow 80/tcp
ufw allow 3000/tcp
ufw allow 443/tcp
ufw reload
```

### Step 8: Access Your Application

Open your browser:
- Frontend: `http://your-vps-ip` or `http://your-domain.com`
- API: `http://your-vps-ip:3000/api`

## 🔧 Docker Commands Reference

### Starting and Stopping

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Restart a specific service
docker-compose restart backend

# View logs
docker-compose logs -f
docker-compose logs backend
```

### Database Management

```bash
# Access MySQL shell
docker-compose exec db mysql -u root -p

# Backup database
docker-compose exec db mysqldump -u root -p football_stats > backup.sql

# Restore database
docker-compose exec -T db mysql -u root -p football_stats < backup.sql

# View database logs
docker-compose logs db
```

### Rebuilding Services

```bash
# Rebuild specific service
docker-compose build backend

# Rebuild all services
docker-compose build

# Rebuild and restart
docker-compose up -d --build
```

## 🔍 Troubleshooting

### Frontend Can't Connect to Backend

**Problem**: Frontend shows connection errors

**Solution**:
1. Check that `VITE_API_URL` in `.env` is correct
2. Rebuild frontend: `docker-compose build frontend`
3. Check backend is running: `docker-compose ps backend`
4. Test API directly: `curl http://localhost:3000/api/championships`

### Database Connection Errors

**Problem**: Backend can't connect to database

**Solution**:
1. Verify database is running: `docker-compose ps db`
2. Check database health: `docker-compose exec db mysqladmin ping -h localhost`
3. Verify credentials in `.env` match
4. Check logs: `docker-compose logs db`

### Port Already in Use

**Problem**: Error binding to port 80 or 3000

**Solution**:
```bash
# Find what's using the port
sudo lsof -i :80
sudo lsof -i :3000

# Stop the conflicting service or change ports in docker-compose.yml
```

### Database Not Initializing

**Problem**: Tables not created on first run

**Solution**:
```bash
# Remove volumes and start fresh
docker-compose down -v
docker-compose up -d

# Manually initialize
docker-compose exec db mysql -u root -p football_stats < database/schema.sql
```

### Permission Errors

**Problem**: Backend can't write files

**Solution**:
```bash
# Fix permissions in backend container
docker-compose exec backend chown -R www-data:www-data /var/www/html
docker-compose exec backend chmod -R 755 /var/www/html
```

## 📊 Service Architecture

```
┌─────────────────┐
│   Frontend      │  Port 80
│   (React/Nginx) │
└────────┬────────┘
         │
         │ HTTP Requests
         ▼
┌─────────────────┐
│   Backend       │  Port 3000
│   (PHP/Apache)  │
└────────┬────────┘
         │
         │ Database Queries
         ▼
┌─────────────────┐
│   Database      │  Port 3306
│   (MySQL 8.0)   │
└─────────────────┘
```

## 🔐 Security Recommendations

1. **Change Default Passwords**: Always use strong, unique passwords in production
2. **Use HTTPS**: Configure SSL/TLS certificates (Let's Encrypt recommended)
3. **Firewall Rules**: Only expose necessary ports
4. **Regular Updates**: Keep Docker images updated
5. **Backup Database**: Set up automated backups
6. **Environment Variables**: Never commit `.env` to version control

## 🔄 Updating the Application

```bash
# Pull latest changes
git pull origin main

# Rebuild and restart services
docker-compose down
docker-compose build
docker-compose up -d
```

## 📈 Monitoring

### Check Resource Usage

```bash
# Container stats
docker stats

# Disk usage
docker system df

# Logs size
du -sh $(docker inspect --format='{{.LogPath}}' football_stats_frontend)
```

### Health Checks

```bash
# Check all services
docker-compose ps

# Test backend API
curl http://localhost:3000/api/championships

# Test frontend
curl http://localhost/
```

## 🆘 Support

For issues or questions:
1. Check logs: `docker-compose logs -f`
2. Verify environment configuration
3. Ensure all ports are available
4. Check firewall settings on VPS

## 📝 Notes for Hostinger VPS

- Hostinger VPS typically comes with Docker pre-installed on some plans
- Make sure you have root access or sudo privileges
- Default SSH port is 22
- Consider using Hostinger's backup service for database backups
- Monitor resource usage as VPS plans have specific limits

---

**🎯 Quick Deploy Command**

For a fresh deployment on Hostinger VPS:

```bash
git clone https://github.com/merchandev/football-stats-system.git && \
cd football-stats-system && \
cp .env.example .env && \
nano .env && \
docker compose build && \
docker compose up -d
```

Remember to edit `.env` before running the last two commands!
