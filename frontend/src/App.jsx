import { BrowserRouter as Router, Routes, Route, Link, useLocation } from 'react-router-dom';
import { useState } from 'react';
import './App.css';

// Import react-icons
import {
  MdDashboard,
  MdPerson,
  MdBarChart,
  MdTrendingUp,
  MdStar
} from 'react-icons/md';
import { IoFootball } from 'react-icons/io5';
import { FaTrophy, FaShieldAlt } from 'react-icons/fa';

// Pages
import Dashboard from './pages/Dashboard';
import Championships from './pages/Championships';
import Teams from './pages/Teams';
import Players from './pages/Players';
import Matches from './pages/Matches';
import ChampionshipStats from './pages/ChampionshipStats';
import TeamStats from './pages/TeamStats';
import PlayerStats from './pages/PlayerStats';

function NavigationItem({ to, icon, children }) {
  const location = useLocation();
  const isActive = location.pathname === to;

  return (
    <Link to={to} className={`nav-item ${isActive ? 'active' : ''}`}>
      <span className="icon">{icon}</span>
      <span className="nav-text">{children}</span>
    </Link>
  );
}

function App() {
  const [sidebarOpen, setSidebarOpen] = useState(true);

  return (
    <Router>
      <div className="app">
        {/* Sidebar */}
        <aside className={`sidebar ${sidebarOpen ? 'open' : 'mini'}`}>
          <div className="sidebar-header">
            <h1>⚽ FutStats</h1>
            <p>Sistema de Estadísticas de Fútbol Femenino</p>
          </div>
          <nav className="sidebar-nav">
            <NavigationItem to="/" icon={<MdDashboard />}>
              Dashboard
            </NavigationItem>

            <div className="nav-section">Gestión de Datos</div>
            <NavigationItem to="/championships" icon={<FaTrophy />}>
              Campeonatos
            </NavigationItem>
            <NavigationItem to="/teams" icon={<FaShieldAlt />}>
              Equipos
            </NavigationItem>
            <NavigationItem to="/players" icon={<MdPerson />}>
              Jugadoras
            </NavigationItem>
            <NavigationItem to="/matches" icon={<IoFootball />}>
              Partidos
            </NavigationItem>

            <div className="nav-section">Estadísticas</div>
            <NavigationItem to="/stats/championships" icon={<MdBarChart />}>
              Por Campeonato
            </NavigationItem>
            <NavigationItem to="/stats/teams" icon={<MdTrendingUp />}>
              Por Equipo
            </NavigationItem>
            <NavigationItem to="/stats/players" icon={<MdStar />}>
              Por Jugadora
            </NavigationItem>
          </nav>

          {/* Toggle Button */}
          <button
            className="sidebar-toggle-btn"
            onClick={() => setSidebarOpen(!sidebarOpen)}
            aria-label={sidebarOpen ? "Minimizar menú" : "Expandir menú"}
          >
            <span className="toggle-icon">{sidebarOpen ? '‹' : '›'}</span>
            <span className="toggle-text">{sidebarOpen ? 'Minimizar' : 'Expandir'}</span>
          </button>
        </aside>

        {/* Main Content */}
        <main className="main-content">
          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/championships" element={<Championships />} />
            <Route path="/teams" element={<Teams />} />
            <Route path="/players" element={<Players />} />
            <Route path="/matches" element={<Matches />} />
            <Route path="/stats/championships" element={<ChampionshipStats />} />
            <Route path="/stats/teams" element={<TeamStats />} />
            <Route path="/stats/players" element={<PlayerStats />} />
          </Routes>

          {/* Footer with Credits */}
          <footer className="app-footer">
            <p className="footer-credit">
              Desarrollado por{' '}
              <a
                href="https://merchan.dev"
                target="_blank"
                rel="noopener noreferrer"
              >
                Merchan.dev
              </a>
            </p>
          </footer>
        </main>
      </div>
    </Router>
  );
}

export default App;
