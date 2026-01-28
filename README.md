# ⚽ Sistema de Estadísticas de Fútbol Femenino

Un sistema completo de gestión y análisis estadístico para fútbol femenino, desarrollado con React, PHP y MySQL.

## 📋 Características

### Gestión de Datos
- ✅ **Campeonatos**: Registrar diversos torneos y formatos de competición
- ✅ **Equipos**: Administrar clubes con información detallada
- ✅ **Jugadoras**: Gestión completa de jugadoras con datos personales y de carrera
- ✅ **Partidos**: Registro detallado de partidos con:
  - Alineaciones (titulares y suplentes)
  - Goles (con asistencias y tipo de gol)
  - Tarjetas amarillas y rojas
  - Minutos jugados por jugadora
  - Directores técnicos y jueces
  - Estadio y fecha/hora

### Estadísticas Avanzadas

#### Por Campeonato
- 📊 Tabla de posiciones completa
- 🥇 Top goleadoras
- 🎯 Top asistidoras
- 🟨🟥 Historial de tarjetas
- ⚖️ Historial de jueces

#### Por Equipo
- 🏆 Títulos ganados
- 📈 Estadísticas por campeonato
- 👥 Jugadoras con más partidos
- ⚽ Goleadoras históricas
- 🆚 Análisis head-to-head contra rivales

#### Por Jugadora
- 🏃 Partidos jugados y minutos
- ⚽ Goles y asistencias
- 🟨🟥 Tarjetas recibidas
- 🎯 Goles por rival

#### Por Director Técnico
- 📊 Estadísticas separadas por club
- ✅ Partidos ganados/empatados/perdidos
- 📈 Porcentaje de efectividad

#### Por Juez
- ⚖️ Partidos arbitrados
- 🟨🟥 Tarjetas emitidas
- 🎯 Penales otorgados

### Exportación
- 📄 **PDF**: Genera reportes profesionales en PDF
- 📊 **Excel**: Exporta datos para análisis adicional

## 🛠️ Tecnologías

### Frontend
- **React 18** con Vite
- **React Router** para navegación
- **Axios** para llamadas API
- **CSS3** con diseño moderno y responsivo

### Backend
- **PHP 8+** con arquitectura RESTful
- **PDO** para acceso seguro a base de datos
- **TCPDF** para generación de PDFs
- **PhpSpreadsheet** para exportación a Excel

### Base de Datos
- **MySQL 8** con esquema optimizado
- Índices para rendimiento
- Relaciones con claves foráneas

## 📦 Instalación

### Requisitos Previos
- Node.js 18+
- PHP 8.0+
- MySQL 8.0+
- Composer
- Apache/Nginx con mod_rewrite

### 1. Configurar Base de Datos

```bash
# Importar el esquema
mysql -u root -p < database/schema.sql
```

### 2. Configurar Backend

```bash
cd backend

# Instalar dependencias PHP
composer install

# Configurar conexión a base de datos
# Editar backend/config/database.php con tus credenciales
```

### 3. Configurar Frontend

```bash
cd frontend

# Instalar dependencias
npm install

# Configurar URL del backend
# Editar src/services/api.js cambiar API_BASE_URL
```

## 🚀 Uso

### Desarrollo

**Backend:**
- Configurar un virtual host de Apache apuntando a `backend/api/`
- Asegurarse de que mod_rewrite esté habilitado
- URL típica: `http://localhost/api/`

**Frontend:**
```bash
cd frontend
npm run dev
```
Acceder a: `http://localhost:5173`

### Producción

**Frontend:**
```bash
cd frontend
npm run build
```
Los archivos de producción estarán en `frontend/dist/`

**Backend:**
- Copiar `backend/` al servidor
- Ejecutar `composer install --no-dev`
- Configurar virtual host en producción

## 📁 Estructura del Proyecto

```
football-stats-system/
├── database/
│   └── schema.sql              # Esquema completo de base de datos
├── backend/
│   ├── config/
│   │   └── database.php        # Configuración de BD
│   ├── models/                 # Modelos de datos
│   │   ├── Championship.php
│   │   ├── Team.php
│   │   ├── Player.php
│   │   ├── Match.php
│   │   ├── Coach.php
│   │   └── Referee.php
│   ├── api/                    # Endpoints REST
│   │   ├── index.php
│   │   ├── championships.php
│   │   ├── teams.php
│   │   ├── players.php
│   │   ├── matches.php
│   │   ├── coaches.php
│   │   └── referees.php
│   ├── utils/                  # Utilidades (PDF, Excel)
│   └── composer.json
└── frontend/
    ├── src/
    │   ├── services/
    │   │   └── api.js          # Cliente API
    │   ├── pages/              # Páginas de la app
    │   │   ├── Dashboard.jsx
    │   │   ├── Championships.jsx
    │   │   ├── Teams.jsx
    │   │   ├── Players.jsx
    │   │   ├── Matches.jsx
    │   │   └── ChampionshipStats.jsx
    │   ├── App.jsx
    │   └── App.css
    └── package.json
```

## 🔌 Endpoints API

### Campeonatos
- `GET /api/championships` - Listar todos
- `GET /api/championships/{id}` - Obtener uno
- `POST /api/championships` - Crear
- `PUT /api/championships/{id}` - Actualizar
- `DELETE /api/championships/{id}` - Eliminar
- `GET /api/championships/standings/{id}` - Tabla de posiciones
- `GET /api/championships/scorers/{id}` - Top goleadoras
- `GET /api/championships/assisters/{id}` - Top asistidoras

### Equipos
- `GET /api/teams` - Listar todos
- `GET /api/teams/{id}` - Obtener uno
- `GET /api/teams/stats/{id}` - Estadísticas
- `GET /api/teams/titles/{id}` - Títulos ganados
- `GET /api/teams/head-to-head/{id1}/{id2}` - Enfrentamientos directos

### Jugadoras
- `GET /api/players` - Listar todas
- `GET /api/players/{id}` - Obtener una
- `GET /api/players/stats/{id}` - Estadísticas de carrera
- `GET /api/players/matches/{id}` - Partidos jugados
- `GET /api/players/goals-by-rival/{id}` - Goles por rival

### Partidos
- `GET /api/matches` - Listar todos
- `GET /api/matches/{id}` - Obtener uno
- `POST /api/matches/goal` - Registrar gol
- `POST /api/matches/card` - Registrar tarjeta
- `POST /api/matches/lineup` - Agregar jugadora a alineación

## 🎨 Diseño

El sistema cuenta con:
- ✨ Diseño moderno con gradientes y animaciones
- 🌙 Tema oscuro profesional
- 📱 Completamente responsivo
- ⚡ Interfaz rápida y fluida
- 🎯 Experiencia de usuario optimizada

## 🔒 Seguridad

- Prepared statements para prevenir SQL injection
- Validación de datos en backend
- CORS configurado para APIs
- Sanitización de inputs

## 🐛 Solución de Problemas

### Error de conexión a base de datos
- Verificar credenciales en `backend/config/database.php`
- Asegurarse de que MySQL esté corriendo
- Verificar que la base de datos `football_stats` exista

### Error 404 en API
- Verificar que mod_rewrite esté habilitado
- Revisar configuración de `.htaccess`
- Verificar la URL base en `frontend/src/services/api.js`

### Frontend no conecta con backend
- Verificar CORS en backend
- Revisar URL de API en `api.js`
- Abrir consola del navegador para ver errores

## 📝 Próximas Características

- [ ] Módulo de coaches y referees completo
- [ ] Exportación avanzada de PDF con gráficos
- [ ] Dashboard con gráficos interactivos
- [ ] Sistema de autenticación de usuarios
- [ ] Historial de momentos importantes
- [ ] Comparación de jugadoras
- [ ] Mapas de calor de goles

## 👥 Contribuir

Este es un proyecto profesional completo listo para producción. Puedes extenderlo con:
- Más tipos de estadísticas
- Gráficos y visualizaciones
- Módulos de predicción
- Integración con APIs externas

## 📄 Licencia

Este proyecto es de código abierto y está disponible para uso personal y comercial.

## 🤝 Soporte

Para soporte técnico o consultas:
- Revisar la documentación de código
- Verificar los comentarios en los archivos
- Consultar la estructura de base de datos en `schema.sql`

---

**Desarrollado con ⚽ para el fútbol femenino**
