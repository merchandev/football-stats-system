# INSTRUCCIONES DE INSTALACIÓN RÁPIDA

## Paso 1: Configurar Base de Datos

1. Abrir phpMyAdmin o línea de comandos MySQL
2. Ejecutar:
```sql
CREATE DATABASE football_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
3. Importar el archivo `database/schema.sql`

## Paso 2: Configurar Backend

1. Editar `backend/config/database.php` con tus credenciales de MySQL
2. Abrir terminal en la carpeta `backend`:
```bash
cd C:\Users\merch\.gemini\antigravity\scratch\football-stats-system\backend
composer install
```

3. Configurar Virtual Host en Apache (ejemplo):
```apache
<VirtualHost *:80>
    DocumentRoot "C:/Users/merch/.gemini/antigravity/scratch/football-stats-system/backend/api"
    ServerName localhost
    
    <Directory "C:/Users/merch/.gemini/antigravity/scratch/football-stats-system/backend/api">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Paso 3: Configurar Frontend

1. Editar `frontend/src/services/api.js` línea 3:
```javascript
const API_BASE_URL = 'http://localhost/api'; // Cambiar según tu configuración
```

2. Abrir terminal en la carpeta `frontend`:
```bash
cd C:\Users\merch\.gemini\antigravity\scratch\football-stats-system\frontend
npm install
npm run dev
```

## Paso 4: Acceder al Sistema

- Frontend: http://localhost:5173
- Backend API: http://localhost/api

## Verificación

1. Abrir el frontend
2. El Dashboard debería mostrar:
   - 3 campeonatos
   - 4 equipos
   - 5 jugadoras
   - 1 partido

Si ves estos números, ¡el sistema está funcionando correctamente!

## Próximos Pasos

1. Explorar el menú lateral
2. Probar crear un nuevo campeonato
3. Ver estadísticas en "Estadísticas > Por Campeonato"
4. Revisar el README.md para documentación completa
